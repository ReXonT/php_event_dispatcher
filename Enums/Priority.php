<?php

namespace Merexo\EventDispatcher\Enums;

final class Priority
{
    const MAX = PHP_INT_MAX;
    const HIGH = 3;
    const MEDIUM = 2;
    const DEFAULT = 1;
    const LOW = 0;
    const MIN = PHP_INT_MIN;
}