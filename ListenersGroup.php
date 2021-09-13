<?php

namespace Merexo\EventDispatcher;

class ListenersGroup
{
    private $event_name;
    private $callback;
    private $object;

    /**
     * ListenersGroup constructor.
     * @param string $event_name
     * @param callable $callback
     * @param EventDispatcher $object
     */
    public function __construct(string $event_name, callable $callback, EventDispatcher $object)
    {
        $this->event_name = $event_name;
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
     * @return string
     */
    public function getEventName(): string
    {
        return $this->event_name;
    }

    /**
     * @param  string|array $listener
     * @param  int $rang
     * @return $this
     */
    public function attach($listener, int $rang = 1)
    {
        $this->object->attach($this->event_name, $listener, $rang);

        return $this;
    }
}