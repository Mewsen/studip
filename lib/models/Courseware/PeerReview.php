<?php

namespace Courseware;

use Course;
use Statusgruppen;
use User;

/**
 * Courseware's peer review instances.
 *
 * @since   Stud.IP 6.0
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @property int $id database column
 * @property int $process_id database column
 * @property int $task_id database column
 * @property string $submitter_id database column
 * @property string $reviewer_id database column
 * @property string|null $reviewer_type database column
 * @property \JSONArrayObject|null $assessment database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property PeerReviewProcess $process belongs_to PeerReviewProcess
 * @property Task $task belongs_to Task
 * @property \User $submitter belongs_to \User
 * @property \User $reviewer belongs_to \User
 */
class PeerReview extends \SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_peer_reviews';

        $config['serialized_fields']['assessment'] = 'JSONArrayObject';

        $config['belongs_to']['process'] = [
            'class_name' => PeerReviewProcess::class,
            'foreign_key' => 'process_id',
        ];
        $config['belongs_to']['task'] = [
            'class_name' => Task::class,
            'foreign_key' => 'task_id',
        ];
        $config['belongs_to']['submitter'] = [
            'class_name' => User::class,
            'foreign_key' => 'submitter_id',
        ];
        $config['belongs_to']['reviewer'] = [
            'class_name' => User::class,
            'foreign_key' => 'reviewer_id',
        ];

        parent::configure($config);
    }

    public static function findByCourse(Course $course): iterable
    {
        $collections = [];
        foreach (PeerReviewProcess::findByCourse($course) as $process) {
            $collections[] = $process->getPeerReviews()->getArrayCopy();
        }

        return array_flatten($collections);
    }

    public function getCourse(): Course
    {
        return $this->process->getCourse();
    }

    public function isAnonymous(): bool
    {
        return $this->process->isAnonymous();
    }

    public function isReviewer(User $user): bool
    {
        return match($this->reviewer_type) {
            'autor' => $this->reviewer_id === $user->id,
            'group' => \Statusgruppen::isMemberOf($this->reviewer_id, $user->getId()),
        };
    }

    public function getReviewer(): User|Statusgruppen
    {
        return match($this->reviewer_type) {
            'autor' => User::find($this->reviewer_id),
            'group' => Statusgruppen::find($this->reviewer_id),
        };
    }

    public function isSubmitter(User $user): bool
    {
        return match (get_class($this->getSubmitter())) {
            Statusgruppen::class => \Statusgruppen::isMemberOf($this->submitter_id, $user->id),
            User::class => $this->submitter_id === $user->id
        };
    }

    public function getSubmitter(): User|Statusgruppen
    {
        return User::find($this->submitter_id)
            ?? Statusgruppen::find($this->submitter_id);
    }
}
