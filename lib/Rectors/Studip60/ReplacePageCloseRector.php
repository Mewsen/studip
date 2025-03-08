<?php
declare(strict_types=1);

namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

final class ReplacePageCloseRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactor(Node $node)
    {
        if (!$this->isName($node->name, 'page_close')) {
            return null;
        }

        return $this->nodeFactory->createMethodCall(
            $this->nodeFactory->createFuncCall('sess'),
            'save'
        );
    }
}
