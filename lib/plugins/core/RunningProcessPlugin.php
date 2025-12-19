<?php

interface RunningProcessPlugin
{
    /**
     * Returns an array of RunningProcess objects, that should be displayed in the running processes widget..
     *
     * @return array : [RunningProcess, RunningProcess, ...]
     */
    public function getRunningProcesses() : array;
}
