<?php

namespace Merexo\EventDispatcher\Interfaces;

interface ListenersInitInterface
{
    /**
     * Implementation of this method loads all parts of the config into one
     */
    public function init();
}