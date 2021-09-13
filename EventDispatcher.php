<?php

namespace Merexo\EventDispatcher;

use Merexo\EventDispatcher\Enums\Priority;
use Merexo\EventDispatcher\Interfaces\EventDispatcherInterface;
use Merexo\EventDispatcher\Interfaces\EventInterface;
use Merexo\EventDispatcher\Interfaces\ListenerProviderInterface;

class EventDispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    /**
     * @var EventDispatcher|null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $listeners = [];
    private $sorted = false;
    private $initialized = false;

    protected function __construct()
    {
        /** Listeners for all events */
        $this->listeners["*"] = [];
    }

    /**
     * @param EventInterface $event
     * @return array
     */
    public static function getListenersForEvent(EventInterface $event): array
    {
        $instance = self::getInstance();
        $instance->initEventGroup($event->name());
        $all = $instance->listeners["*"];
        $listeners = $instance->listeners[$event->name()];

        if ($parents = class_parents($event)) {
            foreach ($parents as $parent) {
                if (isset($instance->listeners[$parent])) {
                    $listeners = array_merge($listeners, (array) $instance->listeners[$parent]);
                }
            }
        }

        return array_merge($all, $listeners) ?? [];
    }

    /**
     * @param EventInterface $event
     * @param bool $async
     */
    public static function dispatch(EventInterface $event, bool $async = false): void
    {
        $instance = self::getInstance();
        foreach ($instance->getListenersForEvent($event) as $listener => $rang) {
            $instance->actionListener($event, $listener, $async);
        }
    }

    /**
     * @return array
     */
    public static function getAllListeners(): array
    {
        $instance = self::getInstance();
        return $instance->listeners;
    }

    /**
     * @param string $event_name
     * @param string|array $listener
     * @param int $rang
     */
    public static function attach(string $event_name, $listener, int $rang = Priority::DEFAULT)
    {
        $instance = self::getInstance();
        if (is_array($listener)) {
            $listener = $instance->transformListenerToString($listener);
        }

        $instance->listeners[$event_name][$listener] = $rang;
        $instance->sorted = false;
        $instance->reSort();
    }

    /**
     * @param string $event_name
     * @param $listener
     */
    public static function detach(string $event_name, $listener)
    {
        $instance = self::getInstance();
        if (is_array($listener)) {
            $listener = $instance->transformListenerToString($listener);
        }

        if (isset($instance->listeners[$event_name][$listener])) {
            unset($instance->listeners[$event_name][$listener]);
            $instance->sorted = false;
            $instance->reSort();
        }
    }

    /**
     * @param string $event_name
     * @param callable $group
     * @return callable|ListenersGroup
     */
    public static function group(string $event_name, Callable $group)
    {
        $group = new ListenersGroup($event_name, $group, self::$instance);
        // Exec callable $group. Check __invoke method in ListenersGroup
        $group();

        return $group;
    }

    /**
     * @param string|array $listener
     * @param callable $group
     * @return callable|EventsGroup
     */
    public static function eventsGroup($listener, Callable $group)
    {
        $group = new EventsGroup($listener, $group, self::$instance);
        // Exec callable $group. Check __invoke method in EventsGroup
        $group();

        return $group;
    }

    /**
     * @return EventDispatcher
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
            self::$instance->initConfig();
            self::$instance->sortListenersByRang();
            self::$instance->initialized = true;
        }

        return self::$instance;
    }

    private function initConfig()
    {
        // TODO create or upload your own config here
    }

    /**
     * @param EventInterface $event
     * @param $class
     * @param $method
     */
    private function asyncCall(EventInterface $event, $class, $method)
    {
        // TODO your async realization here
    }

    private function sortListenersByRang()
    {
        foreach ($this->listeners as $event => $listeners) {
            arsort($this->listeners[$event]);
        }

        $this->sorted = true;
    }

    /**
     * @param string $event
     */
    private function initEventGroup(string $event = '*'): void
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
    }

    /**
     * @param EventInterface $event
     * @param $listener
     * @param bool $async
     */
    private function actionListener(EventInterface $event, $listener, bool $async = false)
    {
        list($class, $method) = $this->transformListenerToArray($listener);

        if (!class_exists($class)) {
            return;
        }

        if ($async) {
            $this->asyncCall($event, $class, $method);
        } else {
            (new $class())->$method($event);
        }
    }

    private function reSort()
    {
        if ($this->initialized and !$this->sorted) {
            $this->sortListenersByRang();
        }
    }

    /**
     * @param array $listener
     * @return string
     */
    private function transformListenerToString(array $listener)
    {
        if (count($listener) == 2) {
            list($class, $method) = $listener;
            return $class . "@" . $method;
        }

        return implode($listener);
    }

    /**
     * @param $listener
     * @return array
     */
    private function transformListenerToArray($listener)
    {
        if (strpos($listener, "@") !== false) {
            list($class, $method) = explode("@", $listener);
        } else {
            $class = $listener;
            $method = "on";
        }

        return [$class, $method];
    }
}