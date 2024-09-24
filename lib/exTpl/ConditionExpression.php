<?php

namespace exTpl;

/**
 * ConditionExpression represents the conditional operator ('?:').
 */
class ConditionExpression implements Expression
{
    protected Expression $condition;
    protected Expression $left;
    protected Expression $right;

    /**
     * Initializes a new Expression instance.
     *
     * @param Expression $condition expression
     * @param Expression $left left alternative
     * @param Expression $right right alternative
     */
    public function __construct(Expression $condition, Expression $left, Expression $right)
    {
        $this->condition = $condition;
        $this->left      = $left;
        $this->right     = $right;
    }

    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): mixed
    {
        return $this->condition->value($context) ?
            $this->left->value($context) : $this->right->value($context);
    }
}
