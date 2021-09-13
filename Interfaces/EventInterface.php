<?php

namespace Merexo\EventDispatcher\Interfaces;

/**
 * Интерфейс для создания ивентов
 */
interface EventInterface
{
    /**
     * Get event name
     *
     * @return string
     */
    public function name();

    /**
     * Get emitter/context from which event was triggered
     *
     * @return null|string|object
     */
    public function emitter();

    /**
     * Get parameters passed to the event
     *
     * @return array
     */
    public function params();

    /**
     * Get a single parameter by name
     *
     * @param  string $name
     * @return mixed
     */
    public function param(string $name);

    /**
     * Set the event name
     *
     * @param  string $name
     * @return void
     */
    public function set_name(string $name);

    /**
     * Set the event emitter
     *
     * @param  null|string|object $target
     * @return void
     */
    public function set_emitter($target);

    /**
     * Set event parameters
     *
     * @param  array $params
     * @return void
     */
    public function set_params(array $params);

    /**
     * Indicate whether or not to stop propagating this event
     *
     * @param  bool $flag
     */
    public function stopPropagation(bool $flag = true);
}