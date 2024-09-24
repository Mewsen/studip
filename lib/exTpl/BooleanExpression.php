<?php

namespace exTpl;

/**
 * BooleanExpression represents a boolean operator.
 */
class BooleanExpression extends BinaryExpression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): bool
    {
        $left  = $this->left->value($context);
        $right = $this->right->value($context);

        return match ($this->operator) {
            T_IS_EQUAL            => $left == $right,
            T_IS_NOT_EQUAL        => $left != $right,
            '<'                   => $left < $right,
            T_IS_SMALLER_OR_EQUAL => $left <= $right,
            '>'                   => $left > $right,
            T_IS_GREATER_OR_EQUAL => $left >= $right,
            T_BOOLEAN_AND         => $left && $right,
            T_BOOLEAN_OR          => $left || $right,
        };
    }
}
