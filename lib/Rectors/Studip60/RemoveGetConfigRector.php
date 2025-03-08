<?php
namespace Studip\Rectors\Studip60;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RemoveGetConfigRector extends AbstractRector
{
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace calls to function "get_config()" with calls to "Config::get()->getValue()',
            [
                new CodeSample(
                    '$value = get_config(\'FOO_BAR\');',
                    '$value = Config::get()->getValue(\'FOO_BAR\');'
                )
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node)
    {
        if (!$this->isName($node->name, 'get_config')) {
            return $node;
        }

        return $this->nodeFactory->createMethodCall(
            $this->nodeFactory->createStaticCall(
                \Config::class,
                'get'
            ),
            'getValue',
            array_map(
                fn($arg) => $this->valueResolver->getValue($arg),
                $node->getArgs()
            )
        );
    }
}
