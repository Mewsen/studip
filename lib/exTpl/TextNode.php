<?php

namespace exTpl;

/**
 * TextNode represents a verbatim text segment.
 */
class TextNode implements Node
{
    protected string $text;

    /**
     * Initializes a new Node instance with the given text.
     *
     * @param string $text verbatim text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * Returns a string representation of this node.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): string
    {
        return $this->text;
    }
}
