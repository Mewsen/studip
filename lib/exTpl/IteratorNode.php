<?php

namespace exTpl;

/**
 * IteratorNode represents a single iterator tag:
 * "{foreach ARRAY [as [KEY =>] VALUE]}...{endforeach}".
 */
class IteratorNode extends ArrayNode
{
    protected Expression $expr;
    protected string $key_name;
    protected string $val_name;

    /**
     * Initializes a new Node instance with the given expression.
     *
     * @param Expression $expr expression object
     * @param string $key_name name of variable on each iteration
     * @param string $val_name name of variable on each iteration
     */
    public function __construct(Expression $expr, string $key_name, string $val_name)
    {
        $this->expr     = $expr;
        $this->key_name = $key_name;
        $this->val_name = $val_name;
    }

    /**
     * Returns a string representation of this node. The IteratorNode
     * renders the node sequence for each value in the expression list.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): string
    {
        $values = $this->expr->value($context);
        $result = '';

        if (is_array($values) && is_int(key($values))) {
            $bindings = [$this->key_name => &$key, $this->val_name => &$value];
            $context  = new Context($bindings, $context);

            foreach ($values as $key => $value) {
                $result .= parent::render(new Context($value, $context));
            }
        } else if (is_array($values) && count($values)) {
            return parent::render(new Context($values, $context));
        } else if ($values) {
            return parent::render($context);
        }

        return $result;
    }
}
