<?php

namespace Studip\Plugins;

use Closure;

interface LegacyRouteStrategy
{
    public function getCallable(string $unconsumedPath): Closure;
}
