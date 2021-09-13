<?php

namespace Merexo\EventDispatcher;

use Merexo\EventDispatcher\Interfaces\EventInterface;
use Merexo\EventDispatcher\Interfaces\StoppableEventInterface;

class Event implements EventInterface, StoppableEventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var null|string|object
     */
    private $emitter;

    /**
     * @var array
     */
    private $params;

    /**
     * @var bool
     */
    private $stop_propagation = false;

    public function __invoke()
    {
        ($this->callback)($this);
    }

    public function __construct(
        string $name,
        array $params = [],
        object $emitter = null,
        bool $stop_propagation = false
    )
    {
        $this->name = $name;
        $this->params = $params;
        $this->emitter = $emitter;
        $this->stop_propagation = $stop_propagation;
    }

    public function name()
    {
        return $this->name;
    }

    public function emitter()
    {
        return $this->emitter;
    }

    public function params()
    {
        return (array)$this->params;
    }

    public function param(string $name)
    {
        return $this->params[$name];
    }

    public function set_name(string $name)
    {
        $this->name = $name;
    }

    public function set_emitter($target)
    {
        $this->emitter = $target;
    }

    public function set_param($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function set_params(array $params)
    {
        $this->params = $params;
    }

    public function stopPropagation(bool $flag = true)
    {
        $this->stop_propagation = $flag;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stop_propagation;
    }

    public static function make($params, $name = null)
    {
        $name = $name ?? static::class;
        return new static($name, $params);
    }
}