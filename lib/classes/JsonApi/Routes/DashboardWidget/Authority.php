<?php

namespace JsonApi\Routes\DashboardWidget;

use User;
use DashboardWidget\Container;
use DashboardWidget\Widget;

/**
 * DashboardWidget's routes Authority class.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class Authority
{
    /**
     * Checks if a user is allowed to fetch miscellaneous dashboard widget data.
     *
     * @param User $user The user to check permissions for
     * @return bool True if the user has at least 'user' permission level, false otherwise
     */
    public static function canFetchMisc(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('user'); // TODO: What to check here?
    }

    /**
     * Checks if a user is allowed to create dashboard widget container.
     *
     * @param User $user The user to check permissions for
     * @return bool True if the user has at least 'user' permission level, false otherwise
     */
    public static function canCreateContainer(User $user): bool
    {
        return $GLOBALS['perm']->have_perm('user'); // TODO: What to check here?
    }

    /**
     * Checks if a user is allowed to get dashboard widget container info.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canShowContainer(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to manage widgets in a container over relationship.
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canManageContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to get the list of widgets from dashboard widget container.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canIndexContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to create new widgets in the container.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canCreateContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to update the widget in the container.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canUpdateContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to delete the widgets in the container.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canDeleteContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to get the widget's info in the container.
     *
     * @param User $user The user to check permissions for
     * @param Container $resource The container
     * @return bool if the user is the owner of the targeted container.
     */
    public static function canShowContainerWidgets(User $user, Container $resource): bool
    {
        return $user->id === $resource->owner->id;
    }

    /**
     * Checks if a user is allowed to get info of a specific widgets.
     *
     * @param User $user The user to check permissions for
     * @param Widget $resource The widget
     * @return bool if the user is the owner of the targeted widget's container.
     */
    public static function canShowWidgets(User $user, Widget $resource): bool
    {
        return $user->id === $resource->container->owner->id;
    }

    /**
     * Checks if a user is allowed to update a specific widgets.
     *
     * @param User $user The user to check permissions for
     * @param Widget $resource The widget
     * @return bool if the user is the owner of the targeted widget's container.
     */
    public static function canUpdateWidgets(User $user, Widget $resource): bool
    {
        return $user->id === $resource->container->owner->id;
    }
}
