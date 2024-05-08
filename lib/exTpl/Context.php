<?php
/**
 * Context.php - template parser symbol table
 *
 * A Context object represents the symbol table used to resolve
 *  symbol names to their values in the local scope. Each context
 *  may inherit symbol definitions from its parent context.
 *
 * @copyright 2013  Elmar Ludwig
 * @license GPL2 or any later version
 */

namespace exTpl;

use Closure;

class Context
{
    private array $bindings;
    private Closure|null $escape;
    private Context|null $parent;

    /**
     * Initializes a new Context instance with the given bindings.
     *
     * @param array $bindings symbol table
     * @param Context|null $parent parent context (or NULL)
     */
    public function __construct(array $bindings, Context $parent = null)
    {
        $this->bindings = $bindings;
        $this->parent   = $parent;
    }

    /**
     * Looks up the value of a symbol in this context and returns it.
     * The reserved symbol "this" is an alias for the current context.
     *
     * @param string $key symbol name
     */
    public function lookup(string $key): mixed
    {
        if (isset($this->bindings[$key])) {
            return $this->bindings[$key];
        } else if ($this->parent) {
            return $this->parent->lookup($key);
        }

        return null;
    }

    /**
     * Enables or disables automatic escaping for template values.
     *
     * @param callable|null $escape escape callback or null
     */
    public function autoescape(?callable $escape): void
    {
        $this->escape = $escape ? $escape(...) : null;
    }

    /**
     * Escapes the given value using the configured strategy.
     *
     * @param mixed $value expression value
     */
    public function escape(mixed $value): mixed
    {
        if (isset($this->escape)) {
            $value = call_user_func($this->escape, $value);
        } else if ($this->parent) {
            $value = $this->parent->escape($value);
        }

        return $value;
    }
}
