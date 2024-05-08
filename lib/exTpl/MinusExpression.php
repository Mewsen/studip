<?php

namespace exTpl;

/**
 * MinusExpression represents the unary minus operator ('-').
 */
class MinusExpression extends UnaryExpression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context  symbol table
     */
    public function value(Context $context): mixed
    {
        return -$this->expr->value($context);
    }
}
