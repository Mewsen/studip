<?php
namespace Studip\Processes;

interface ProcessProvider
{

    /**
     * Returns an array of RunningProcess objects for the given user.
     *
     * @return array : RunningProcess[]
     */
   public static function getProcesses(\User $user): array;
}
