<?php
namespace Studip\Lti\LTI1p3;

use Grading\Instance;
use OAT\Library\Lti1p3Ags\Model\Score\ScoreInterface;
use OAT\Library\Lti1p3Ags\Repository\ScoreRepositoryInterface;

final class ScoreRepository implements ScoreRepositoryInterface
{
    public function save(ScoreInterface $score): ScoreInterface
    {
        $userId = $score->getUserIdentifier();
        $definitionId = $score->getLineItemIdentifier();

        $grade = Instance::findOneBySQL(
            '`definition_id` = :definition_id AND `user_id` = :user_id',
            ['definition_id' => $definitionId, 'user_id' => $userId]
        );
        if (!$grade) {
            $grade = new Instance();
            $grade->definition_id = $definitionId;
            $grade->user_id       = $userId;
        }
        $grade->rawgrade = $score->getScoreGiven();
        $grade->feedback = $score->getComment();
        $grade->store();
        return $score;
    }
}
