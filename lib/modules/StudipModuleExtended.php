<?php

interface StudipModuleExtended extends StudipModule
{

    public function getManyIconNavigation(array $course_ids, array $visits, string $user_id = null): array;

}
