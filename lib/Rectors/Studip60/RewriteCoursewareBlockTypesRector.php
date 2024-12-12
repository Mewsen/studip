<?php
declare(strict_types=1);

namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;

final class RewriteCoursewareBlockTypesRector extends AbstractRector
{

    public function getNodeTypes(): array
    {
        return [StaticCall::class, Class_::class];
    }

    /**
     * @param StaticCall|Class_ $node
     */
    public function refactor(Node $node)
    {
        if ($this->shouldSkip($node)) {
            return null;
        }

        if ($node instanceof Class_) {
            $this->traverseNodesWithCallable(
                $node->getMethods(),
                [$this, 'refactor']
            );

            return null;
        } elseif ($node instanceof ClassMethod) {
            $node->returnType = new Node\Identifier('string');
            return $node;
        } elseif ($node instanceof StaticCall) {
            return $node->args[0]->value;
        }

    }

    public function shouldSkip(Node $node): bool
    {
        return !(
            $node instanceof Class_
            && $this->isName($node->extends, 'Courseware\BlockTypes\BlockType')
        ) && !(
            $node instanceof ClassMethod
            && $this->isName($node->name, 'getJsonSchema')
            && $this->isName($node->returnType, 'Opis\JsonSchema\Schema')
        ) && !(
            $node instanceof StaticCall
            && $this->isName($node->class, 'Opis\JsonSchema\Schema')
            && $this->isName($node->name, 'fromJsonString')
        );
    }
}
