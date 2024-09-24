<?php
namespace Studip\Debug;

final class DebugBar
{
    public static function isActivated(): bool
    {
        return \Studip\ENV === 'development'
            && ($_ENV['DEBUG_BAR'] ?? false)
            && class_exists(\DebugBar\DebugBar::class);
    }
}
