<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\Score\ScoreInterface;
use OAT\Library\Lti1p3Ags\Repository\ScoreRepositoryInterface;

class ScoreRepository implements ScoreRepositoryInterface
{

    public function save(ScoreInterface $score): ScoreInterface
    {
        $user_id = $score->getUserIdentifier();
        $definition_id = $score->getLineItemIdentifier();

        $grade = \Grading\Instance::findBySQL(
            '`definition_id` = :definition_id AND `user_id` = :user_id',
            ['definition_id' => $definition_id, 'user_id' => $user_id]
        );
        if (!$grade) {
            $grade = new \Grading\Instance();
            $grade->definition_id = $definition_id;
            $grade->user_id       = $user_id;
        }
        $grade->rawgrade = $score->getScoreGiven();
        $grade->feedback = $score->getComment();
        $grade->store();
        return $score;
    }
}
