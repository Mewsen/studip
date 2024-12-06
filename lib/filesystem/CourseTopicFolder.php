<?php
/**
 * CourseTopicFolder.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author    André Noack <noack@data-quest.de>
 * @copyright 2016 Stud.IP Core-Group
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category  Stud.IP
 */
class CourseTopicFolder extends PermissionEnabledFolder
{

    public static $sorter = 1;

    private $topic;


    public static function getTypeName(): string
    {
        return _('Themen-Ordner');
    }

    public static function availableInRange(SimpleORMap|string $range_id_or_object, string $user_id): bool
    {
        $course = Course::toObject($range_id_or_object);
        if ($course && !$course->isNew()) {
            return Seminar_Perm::get()->have_studip_perm('tutor', $course->id, $user_id) && CourseTopic::countBySql("seminar_id = ?" , [$course->id]);
        }
        return false;
    }

    public function __construct($folderdata = null)
    {
        parent::__construct($folderdata);
        $this->getTopic();
    }

    public function getIcon(string $role = Icon::DEFAULT_ROLE): Icon
    {
        return Icon::create(
            count($this->getFiles()) ? 'folder-topic-full' : 'folder-topic-empty',
            $role
        );
    }

    public function getTopic(): ?CourseTopic
    {
        if (isset($this->folderdata['data_content']['topic_id'])) {
            if ($this->topic === null) {
                $this->topic = CourseTopic::find($this->folderdata['data_content']['topic_id']);
            }
            if ($this->topic) {
                $this->folderdata['name']        = (string) $this->topic->title;
                $this->folderdata['description'] = (string) $this->topic->description;
            } else {
                $this->folderdata['name'] = _('(Thema gelöscht)') . ' ' . $this->folderdata['name'];
            }
            return $this->topic;
        }

        return null;
    }

    /**
     * @param CourseTopic $topic
     * @return CourseTopic
     */
    public function setTopic(CourseTopic $topic): ?CourseTopic
    {
        $this->topic = $topic;
        $this->folderdata['data_content']['topic_id'] = $this->topic->id;
        return $this->getTopic();
    }

    /**
     * This method returns the special part for the edit template for the folder type GroupFolder
     *
     * @return \Flexi\Template|string|null  A edit template for a instance of the type GroupFolder
     */
    public function getEditTemplate(): \Flexi\Template|string|null
    {
        $template = $GLOBALS['template_factory']->open('filesystem/topic_folder/edit.php');
        $template->set_attribute('topic', $this->getTopic());
        $template->set_attribute('folder', $this);
        return $template;
    }

    /**
     * Stores the data which was edited in the edit template
     *
     * @return FolderType|MessageBox The template with the edited data
     */
    public function setDataFromEditTemplate(array|ArrayAccess|Request $folderdata): FolderType|MessageBox
    {
        $topic = CourseTopic::find($folderdata['topic_id']);
        if ($topic === null) {
            return MessageBox::error(_('Es wurde kein Thema ausgewählt.'));
        } else {
            if ($this->getTopic() && $topic->id === $this->getTopic()->id) {
                if (!$folderdata['name']) {
                    return MessageBox::error(_('Die Bezeichnung des Ordners fehlt.'));
                }
                $topic->title = $folderdata['name'];
                $topic->description = $folderdata['description'] ?: '';
                $topic->store();
            }
            $this->setTopic($topic);
        }

        if (isset($folderdata['course_topic_folder_perm_write'])) {
            $this->folderdata['data_content']['permission'] = 7;
        } else {
            $this->folderdata['data_content']['permission'] = 5;
        }
        return $this;
    }

    /**
     * Returns the description template for a instance of a GroupFolder type
     *
     * @return \Flexi\Template|string|null A description template for a instance of the type GroupFolder
     */
    public function getDescriptionTemplate(): \Flexi\Template|string|null
    {

        $template = $GLOBALS['template_factory']->open('filesystem/topic_folder/description.php');
        $template->type       = self::getTypeName();
        $template->folder     = $this;
        $template->topic      = $this->getTopic();
        $template->folderdata = $this->folderdata;

        return $template;
    }

    /**
     * @see FolderType::copySettings()
     */
    public function copySettings(): array
    {
        return ['description' => $this->description];
    }


    public function countDownloads(): bool
    {
        return true;
    }

    public function displayDownloads(): bool
    {
        return true;
    }
}
