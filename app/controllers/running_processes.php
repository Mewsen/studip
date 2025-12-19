<?php

class RunningProcessesController extends AuthenticatedController
{

    public function widget_action()
    {
        $this->processes = array_merge(
            \Studip\Processes\Questionnaires::getProcesses(User::findCurrent()),
            \Studip\Processes\TimedFolders::getProcesses(User::findCurrent())
        );
        $plugins = PluginManager::getInstance()->getPlugins('RunningProcessPlugin');
        foreach ($plugins as $plugin) {
            $this->processes = array_merge($this->processes, $plugin->getRunningProcesses());
        }

        $this->contexts = [];
        foreach ($this->processes as $process) {
            if (!isset($this->contexts[$process->context_id])) {
                $context = get_object_by_range_id($process->context_id);

                if ($context) {
                    if ($context instanceof Course) {
                        $avatar = CourseAvatar::getAvatar($process->context_id);
                    } else {
                        $avatar = InstituteAvatar::getAvatar($process->context_id);
                    }
                    $this->contexts[$process->context_id] = [
                        'id' => $process->context_id,
                        'name' => (string) $context->name,
                        'url' => URLHelper::getURL('dispatch.php/course/go', ['to' => $process->context_id]),
                        'avatar' => $avatar->getURL(Avatar::SMALL),
                    ];
                }
            }
        }

        $this->render_vue_app(
            Studip\VueApp::create('RunningProcesses')
                ->withProps([
                    'contexts' => $this->contexts,
                    'processes' => array_map(function ($process) { return $process->toArray(); }, $this->processes)
                ])
        );
    }
}
