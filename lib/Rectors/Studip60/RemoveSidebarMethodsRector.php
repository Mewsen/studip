<?php
// TODO: Assignment!
declare(strict_types=1);

namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RemoveSidebarMethodsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated sidebar methods',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
Sidebar::setImage('foo.gif');
$foo = Sidebar::getImage();
Sidebar::removeImage();
CODE_SAMPLE,
                    ''
                )
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param Expression $node
     * @return int|null
     */
    public function refactor(Node $node): ?int
    {
        $expr = $node->expr;
        if (!$expr instanceof StaticCall) {
            return null;
        }

        if (!$this->isName($expr->class, 'Sidebar')) {
            return null;
        }

        $methodsToRemove = ['setImage', 'getImage', 'removeImage'];
        if ($this->isNames($expr->name, $methodsToRemove)) {
            return NodeVisitor::REMOVE_NODE;
        }

        return null;
    }
}
