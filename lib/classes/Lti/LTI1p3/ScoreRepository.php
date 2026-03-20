<?php
namespace Studip\Lti\LTI1p3;

use Grading\Instance;
use OAT\Library\Lti1p3Ags\Model\Score\ScoreInterface;
use OAT\Library\Lti1p3Ags\Repository\ScoreRepositoryInterface;

final class ScoreRepository implements ScoreRepositoryInterface
{
    public function save(ScoreInterface $score): ScoreInterface
    {
         Instance::updateOrCreate(
            [
                'definition_id' => $score->getLineItemIdentifier(),
                'user_id' => $score->getUserIdentifier()
            ],
            [
                'rawgrade' => $score->getScoreGiven() / 100,
                'feedback' => $score->getComment(),
                'passed' => ScoreRepository::isPassed($score->getGradingProgressStatus()),
                'chdate' => $score->getTimestamp()->getTimestamp()
            ]
        );

        return $score;
    }

    private static function isPassed(string $gradingProgressStatus): bool
    {
        return $gradingProgressStatus === ScoreInterface::GRADING_PROGRESS_STATUS_FULLY_GRADED;
    }
}
