<?php

namespace Merexo\EventDispatcher;

use Merexo\EventDispatcher\Enums\Priority;

class EventsGroup
{
    private $listener;
    private $callback;
    private $object;

    /**
     * EventsGroup constructor.
     * @param string|array $listener
     * @param callable $callback
     * @param EventDispatcher $object
     */
    public function __construct($listener, callable $callback, EventDispatcher $object)
    {
        $this->listener = $listener;
        $this->callback = $callback;
        $this->object = $object;
    }

    /**
     * Выполняет заданный юзером callback при вызове ListenersGroup как функции
     */
    public function __invoke()
    {
        ($this->callback)($this);
    }

    /**
     * @param  string $event_name
     * @param  int $rang
     * @return $this
     */
    public function attach($event_name, int $rang = Priority::DEFAULT)
    {
        $this->object->attach($event_name, $this->listener, $rang);

        return $this;
    }
}