<?php
declare(strict_types=1);

namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;

final class RemoveIncludesRector extends AbstractRector implements ConfigurableRectorInterface
{
    private array $removeIncludes = [];

    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param Expression $node
     */
    public function refactor(Node $node): ?int
    {
        if (!$node->expr instanceof Include_) {
            return null;
        }

        if (!$this->matches($node->expr->expr)) {
            return null;
        }

        return self::REMOVE_NODE;
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $this->removeIncludes = $configuration;
    }

    private function matches(String_ $expr): bool
    {
        foreach ($this->removeIncludes as $removeInclude) {
            if (str_contains($expr->value, $removeInclude)) {
                return true;
            }
        }

        return false;
    }
}
