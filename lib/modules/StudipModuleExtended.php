<?php

interface StudipModuleExtended extends StudipModule
{

    public function getManyIconNavigation(array $course_ids, string $user_id = null): array;

}
