<?php

namespace exTpl;

/**
 * ConstantExpression represents a literal value.
 */
class ConstantExpression implements Expression
{
    protected mixed $value;

    /**
     * Initializes a new Expression instance.
     *
     * @param mixed $value expression value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): mixed
    {
        return $this->value;
    }
}
