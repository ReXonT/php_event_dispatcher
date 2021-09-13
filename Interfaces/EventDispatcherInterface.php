<?php

namespace Merexo\EventDispatcher\Interfaces;

/**
 * PSR-14 Interface с изменением object -> EventInterface typehint и static, + async flag
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-14-event-dispatcher.md
 */
interface EventDispatcherInterface
{
    public static function dispatch(EventInterface $event, bool $async = false);
}