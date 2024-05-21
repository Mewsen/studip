<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\Result\ResultCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\Result\ResultInterface;
use OAT\Library\Lti1p3Ags\Repository\ResultRepositoryInterface;

class ResultRepository implements ResultRepositoryInterface
{

    public function findCollectionByLineItemIdentifier(string $lineItemIdentifier, ?int $limit = null, ?int $offset = null): ResultCollectionInterface
    {
        // TODO: Implement findCollectionByLineItemIdentifier() method.
    }

    public function findByLineItemIdentifierAndUserIdentifier(string $lineItemIdentifier, string $userIdentifier): ?ResultInterface
    {
        // TODO: Implement findByLineItemIdentifierAndUserIdentifier() method.
    }
}
