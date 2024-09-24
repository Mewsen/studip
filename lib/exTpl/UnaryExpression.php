<?php

namespace exTpl;

/**
 * UnaryExpression represents a unary operator.
 */
abstract class UnaryExpression implements Expression
{
    protected Expression $expr;

    /**
     * Initializes a new Expression instance.
     *
     * @param Expression $expr  expression object
     */
    public function __construct(Expression $expr)
    {
        $this->expr = $expr;
    }
}
