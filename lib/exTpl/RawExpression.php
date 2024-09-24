<?php

namespace exTpl;

/**
 * RawExpression represents the "raw" filter function.
 */
class RawExpression extends UnaryExpression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): mixed
    {
        return $this->expr->value($context);
    }
}
