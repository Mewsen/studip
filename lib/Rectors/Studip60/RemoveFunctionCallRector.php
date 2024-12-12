<?php
declare(strict_types=1);

namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use RectorPrefix202411\Webmozart\Assert\Assert;

final class RemoveFunctionCallRector extends AbstractRector implements ConfigurableRectorInterface
{
    private array $removes = [];

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactor(Node $node)
    {
        if (!$this->isNames($node, $this->removes)) {
            return null;
        }

        if (!isset($node->args[0])) {
            return null;
        }

        return $node->args[0]->value;
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allString($configuration);
        Assert::isList($configuration);
        $this->removes = $configuration;
    }
}
