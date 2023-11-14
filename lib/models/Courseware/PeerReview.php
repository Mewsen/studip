<?php

namespace Courseware;

use Course;
use DBManager;
use Statusgruppen;
use User;

/**
 * Courseware's peer review instances.
 *
 * @since   Stud.IP 5.5
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
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
        switch ($this->reviewer_type) {
            case 'autor':
                return $this->reviewer_id === $user->getId();
            case 'group':
                return \Statusgruppen::isMemberOf($this->reviewer_id, $user->getId());
        }
    }

    public function getReviewer(): User|Statusgruppen
    {
        switch ($this->reviewer_type) {
            case 'autor':
                return User::find($this->reviewer_id);
            case 'group':
                return Statusgruppen::find($this->reviewer_id);
        }
    }

    public function isSubmitter(User $user): bool
    {
        return $this->submitter_id === $user->id;
    }

    public function getSubmitter(): User|Statusgruppen
    {
        $user = User::find($this->submitter_id);
        if ($user) {
            return $user;
        }

        $statusGroup = Statusgruppen::find($this->submitter_id);
        return $statusGroup;
    }
}
