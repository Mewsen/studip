<?php

namespace exTpl;

/**
 * NotExpression represents the logical negation operator ('!').
 */
class NotExpression extends UnaryExpression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): bool
    {
        return !$this->expr->value($context);
    }
}
