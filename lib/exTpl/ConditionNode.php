<?php

namespace exTpl;

/**
 * ConditionNode represents a single condition tag:
 * "{if CONDITION}...{else}...{endif}".
 */
class ConditionNode extends ArrayNode
{
    protected Expression $condition;
    protected ArrayNode|null $else_node = null;

    /**
     * Initializes a new Node instance with the given expression.
     *
     * @param Expression $condition expression object
     */
    public function __construct(Expression $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Adds an else block to this condition node.
     */
    public function addElse(): void
    {
        $this->else_node = new ArrayNode();
    }

    /**
     * Adds a child node to this condition node.
     *
     * @param Node $node child node to add
     */
    public function addChild(Node $node): void
    {
        if ($this->else_node) {
            $this->else_node->addChild($node);
        } else {
            parent::addChild($node);
        }
    }

    /**
     * Returns a string representation of this node.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): string
    {
        if ($this->condition->value($context)) {
            return parent::render($context);
        }

        return $this->else_node ? $this->else_node->render($context) : '';
    }
}
