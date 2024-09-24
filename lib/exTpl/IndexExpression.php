<?php

namespace exTpl;

/**
 * IndexExpression represents the array index operator.
 */
class IndexExpression extends BinaryExpression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): mixed
    {
        $left  = $this->left->value($context);
        $right = $this->right->value($context);

        return $left[$right];
    }
}
