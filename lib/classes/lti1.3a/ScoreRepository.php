<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\Score\ScoreInterface;
use OAT\Library\Lti1p3Ags\Repository\ScoreRepositoryInterface;

class ScoreRepository implements ScoreRepositoryInterface
{

    public function save(ScoreInterface $score): ScoreInterface
    {
        // TODO: Implement save() method.
    }
}
