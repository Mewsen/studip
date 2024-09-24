<?php

namespace exTpl;

/**
 * SymbolExpression represents a symbol (template variable).
 */
class SymbolExpression implements Expression
{
    protected string $name;

    /**
     * Initializes a new Expression instance.
     *
     * @param string $name      symbol name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of this symbol.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of this expression.
     *
     * @param Context $context  symbol table
     */
    public function value(Context $context): mixed
    {
        return $context->lookup($this->name);
    }
}
