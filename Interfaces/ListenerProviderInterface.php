<?php

namespace Merexo\EventDispatcher\Interfaces;

/**
 * PSR-14 Interface с изменением object -> EventInterface typehint
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-14-event-dispatcher.md
 */
interface ListenerProviderInterface
{
    public static function getListenersForEvent(EventInterface $event): iterable;
}