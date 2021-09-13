<?php

namespace Merexo\EventDispatcher\Interfaces;

interface ListenerInterface
{
    public function on(EventInterface $event);
}