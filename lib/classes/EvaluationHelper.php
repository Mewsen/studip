<?php

class EvaluationHelper
{
    public static function isPermittedEvaluationAccess(): bool
    {
        $user = User::findCurrent();
        return
            PluginManager::getInstance()->getPlugin(CoreEvaluation::class) &&
            isset($user) &&
            ($user->hasPermissionLevel('root') ||
                $user->hasRole('Zentraler Evaluationsadmin'));
    }
}
