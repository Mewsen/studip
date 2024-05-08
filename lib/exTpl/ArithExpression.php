<?php

namespace exTpl;

/**
 * ArithExpression represents an arithmetic operator.
 */
class ArithExpression extends BinaryExpression
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

        return match ($this->operator) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $left / $right,
            '%' => $left % $right,
            '~' => $left . $right,
        };
    }
}
