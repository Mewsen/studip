<?php

interface StudipModuleExtended extends StudipModule
{

    public const ICON_NAV_CACHE_PATH = 'modules_icon/';

    public function getManyIconNavigation(array $course_ids, array $visits, string $user_id = null): array;

    public function initializeUpdateObserver();

}
