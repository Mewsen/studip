<?php

namespace Studip\Processes;

class TimedFolders implements ProcessProvider
{
    /**
     * Retrieves an array of timed folder processes that are visible, active, and linked to the given user.
     * The method fetches folders of type "TimedFolder" for which the user has access,
     * processes each folder to check its visibility and end time, and compiles them into a list
     * of ongoing processes with relevant details such as folder name, start time, end time, and file information.
     *
     * @return array : RunningProcess[]
     */
    public static function getProcesses(\User $user) : array
    {
        $folders = \Folder::findBySQL("LEFT JOIN `seminar_user` ON (`seminar_user`.`Seminar_id` = `folders`.`range_id` AND `folders`.`range_type` = 'course')
                LEFT JOIN `user_inst` ON (`user_inst`.`Institut_id` = `folders`.`range_id` AND `folders`.`range_type` = 'institute')
            WHERE `folders`.`folder_type` = 'TimedFolder'
                AND (`seminar_user`.`user_id` = :user_id OR `user_inst`.`user_id` = :user_id)
        ", ['user_id' => $user->id]);
        $result = [];
        foreach ($folders as $folder) {
            $folderType = $folder->getTypedFolder();
            if ($folderType->isVisible($user->id) && ($folderType->end_time > 0)) {
                $files = $folderType->getFiles();
                $result[] = new \RunningProcess(
                    $folder->range_id,
                    $folderType->getIcon(),
                    _('Zeitgesteuerter Ordner'),
                    \URLHelper::getURL('dispatch.php/course/files/index/'.$folder->id, ['cid' => $folder->range_id]),
                    $folderType->start_time,
                    $folderType->end_time,
                    false,
                    $folder->name,
                    $folderType->isReadable($user->id) && (count($files) > 0) ? sprintf(ngettext('%d Datei', '%d Dateien', count($files)), count($files)) : ''
                );
            }
        }
        return $result;
    }
}
