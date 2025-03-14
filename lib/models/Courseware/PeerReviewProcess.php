<?php

namespace Courseware;

use Course;
use DBManager;
use SimpleORMapCollection;
use User;

/**
 * A PeerReviewProcess groups a set of PeerReviews.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @since   Stud.IP 6.0
 *
 * @property int $id database column
 * @property int $task_group_id database column
 * @property string $owner_id database column
 * @property \JSONArrayObject $configuration database column
 * @property int $review_start database column
 * @property int $review_end database column
 * @property int|null $paired_at database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \SimpleORMapCollection<PeerReview> $_peer_reviews has_many PeerReview
 * @property TaskGroup $task_group belongs_to TaskGroup
 * @property \User $owner belongs_to \User
 * @property mixed $peer_reviews additional field
 */
class PeerReviewProcess extends \SimpleORMap
{
    public const DEFAULT_DURATION = 7;

    public const STATE_BEFORE = 'before';
    public const STATE_ACTIVE = 'active';
    public const STATE_AFTER = 'after';

    protected static function configure($config = [])
    {
        $config['db_table'] = 'cw_peer_review_processes';

        $config['serialized_fields']['configuration'] = 'JSONArrayObject';

        $config['belongs_to']['task_group'] = [
            'class_name' => TaskGroup::class,
            'foreign_key' => 'task_group_id',
        ];
        $config['belongs_to']['owner'] = [
            'class_name' => User::class,
            'foreign_key' => 'owner_id',
        ];

        $config['additional_fields']['peer_reviews'] = [
            'get' => 'getPeerReviews',
            'set' => false,
        ];

        $config['has_many']['_peer_reviews'] = [
            'class_name' => PeerReview::class,
            'assoc_foreign_key' => 'process_id',
            'on_delete' => 'delete',
            'on_store' => 'store',
            'order_by' => 'ORDER BY mkdate',
        ];

        parent::configure($config);
    }

    public static function findByCourse(Course $course): iterable
    {
        return self::findBySQL('task_group_id IN (?) ORDER BY mkdate', [
            DBManager::get()->fetchFirst('SELECT id FROM `cw_task_groups` WHERE seminar_id = ?', [$course->getId()]),
        ]);
    }

    public static function findByUser(User $user): iterable
    {
        return self::findMany(
            DBManager::get()->fetchFirst(
                'SELECT id FROM cw_peer_review_processes
                   WHERE task_group_id IN (
                     SELECT id FROM cw_task_groups
                       WHERE cw_task_groups.seminar_id IN (
                         SELECT seminar_id FROM seminar_user WHERE user_id = ?))',
                [$user->getId()]
            )
        );
    }

    public function getCourse(): Course
    {
        return $this->task_group->course;
    }

    public function getPeerReviews(): SimpleORMapCollection
    {
        $this->checkAutomaticPairing();

        return SimpleORMapCollection::createFromArray(
            PeerReview::findBySql('process_id = ? ORDER BY mkdate', [$this->getId()])
        );
    }

    public function getDuration(): int
    {
        if (!isset($this->configuration['duration'])) {
            return self::DEFAULT_DURATION;
        }

        return (int) $this->configuration['duration'];
    }

    public function isAnonymous(): bool
    {
        if (!isset($this->configuration['anonymous'])) {
            return true;
        }

        return (bool) $this->configuration['anonymous'];
    }

    public function isAutomaticPairing(): bool
    {
        if (!isset($this->configuration['automaticPairing'])) {
            return true;
        }

        return (bool) $this->configuration['automaticPairing'];
    }

    public function getCurrentState(int $date = null): string
    {
        if (is_null($date)) {
            $date = time();
        }

        if ($this->review_end < $date) {
            return self::STATE_AFTER;
        }

        if ($date < $this->review_start) {
            return self::STATE_BEFORE;
        }

        return self::STATE_ACTIVE;
    }

    public function checkAutomaticPairing(): void
    {
        if ($this->isAutomaticPairing() && !$this->paired_at) {
            $now = time();
            if ($now > $this->review_start) {
                $this->createAutomaticPairings();
                $this->content['paired_at'] = $now;
                $this->content_db['paired_at'] = $now;
                $stmt = \DBManager::get()->prepare(
                    'UPDATE `' . $this->db_table() . '` SET `paired_at` = ? WHERE id = ?'
                );
                $stmt->execute([$now, $this->getId()]);
            }
        }
    }

    public function createAutomaticPairings(): iterable
    {
        $taskGroup = $this->task_group;
        $submitters = $taskGroup->getSubmitters();

        if (count($submitters) < 2) {
            return [];
        }

        shuffle($submitters);
        $copy = $submitters;
       $copy[] = array_shift($copy);
        $pairings = array_map(null, $submitters, $copy);

        return array_map(function ($pairing) use ($taskGroup) {
            [$submitter, $reviewer] = $pairing;
            $task = $taskGroup->findTaskBySolver($submitter);

            return PeerReview::create([
                'process_id' => $this->getId(),
                'task_id' => $task->getId(),
                'submitter_id' => $submitter->getId(),
                'reviewer_id' => $reviewer->getId(),
                'reviewer_type' => $reviewer instanceof User ? 'autor' : 'group',
            ]);
        }, $pairings);
    }

    public function rescheduleTo(int $newStartDate): void
    {
        $newEndDate = $newStartDate + $this->getDuration() * (24 * 60 * 60);
        $this->setData([
            'review_start' => $newStartDate,
            'review_end' => $newEndDate,
        ]);
        $this->store();
    }
}
