<?php

namespace exTpl;

/**
 * ExpressionNode represents an expression tag: "{...}".
 */
class ExpressionNode implements Node
{
    protected Expression $expr;

    /**
     * Initializes a new Node instance with the given expression.
     *
     * @param Expression $expr expression object
     */
    public function __construct(Expression $expr)
    {
        $this->expr = $expr;
    }

    /**
     * Returns a string representation of this node.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): ?string
    {
        $value = $this->expr->value($context);

        if (!($this->expr instanceof RawExpression)) {
            $value = $context->escape($value);
        }

        return $value;
    }
}
