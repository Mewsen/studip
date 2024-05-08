<?php

namespace exTpl;

/**
 * ArrayNode represents a sequence of arbitrary nodes.
 */
class ArrayNode implements Node
{
    protected array $nodes = [];

    /**
     * Adds a child node to this sequence node.
     *
     * @param Node $node child node to add
     */
    public function addChild(Node $node): void
    {
        $this->nodes[] = $node;
    }

    /**
     * Returns a string representation of this node.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): string
    {
        $result = '';

        foreach ($this->nodes as $node) {
            $result .= $node->render($context);
        }

        return $result;
    }
}
