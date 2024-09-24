<?php

namespace exTpl;

/**
 * BinaryExpression represents a binary operator.
 */
abstract class BinaryExpression implements Expression
{
    protected Expression $left;
    protected Expression $right;
    protected mixed $operator;

    /**
     * Initializes a new Expression instance.
     *
     * @param Expression $left left operand
     * @param Expression $right right operand
     * @param mixed $operator operator token
     */
    public function __construct(Expression $left, Expression $right, mixed $operator)
    {
        $this->left     = $left;
        $this->right    = $right;
        $this->operator = $operator;
    }
}
