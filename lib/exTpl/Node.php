<?php
/*
 * Node.php - template parser node interface and classes
 *
 * Copyright (c) 2013  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

namespace exTpl;


/**
 * Basic interface for nodes in the template parse tree. The only
 * required method is "render" to render a node and its children.
 */
interface Node
{
    /**
     * Returns a string representation of this node.
     *
     * @param Context $context symbol table
     */
    public function render(Context $context): ?string;
}
