<?php

namespace exTpl;

/**
 * FunctionExpression represents a function call.
 */
class FunctionExpression implements Expression
{
    protected Expression $name;
    protected array $arguments;

    /**
     * Initializes a new Expression instance.
     *
     * @param Expression $name function name
     * @param array $arguments function arguments
     */
    public function __construct(Expression $name, array $arguments)
    {
        $this->name      = $name;
        $this->arguments = $arguments;
    }

    /**
     * Returns the value of this expression.
     *
     * @param Context $context symbol table
     */
    public function value(Context $context): mixed
    {
        $callable  = $this->name->value($context);
        $arguments = [];

        foreach ($this->arguments as $expr) {
            $arguments[] = $expr->value($context);
        }

        if (is_callable($callable)) {
            return $callable(...$arguments);
        }

        return null;
    }
}
