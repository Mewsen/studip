<?php

namespace JsonApi\Routes\Wiki;

class Authority
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canIndexWiki(\User $user, \Range $range)
    {
        if (!($range instanceof \Course || $range instanceof \Institute)) {
            return false;
        }

        return $range->isAccessibleToUser($user->id);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canShowWiki(\User $user, \WikiPage $wikiPage)
    {
        return $wikiPage->isReadable($user->id);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canCreateWiki(\User $user, $range)
    {
        $config = \RangeConfig::get($range->id);
        return $config->WIKI_CREATE_PERMISSION === 'all'
            || $GLOBALS['perm']->have_studip_perm($config->WIKI_CREATE_PERMISSION, $range->id);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canUpdateWiki(\User $user, \WikiPage $wikiPage)
    {
        return $wikiPage->isEditable($user->id);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canDeleteWiki(\User $user, \WikiPage $wikiPage)
    {
        return $wikiPage->isEditable($user->id);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function canUpdateParent(\User $user, \WikiPage $wikiPage)
    {
        return self::canUpdateWiki($user, $wikiPage);
    }
}
