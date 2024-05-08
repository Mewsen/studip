<?php
/*
 * Expression.php - template parser expression interface and classes
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
 * Basic interface for expressions in the template parse tree. The
 * only required method is "value" to get the expression's value.
 */
interface Expression
{
    /**
     * Returns the value of this expression.
     *
     * @param Context $context  symbol table
     */
    public function value(Context $context): mixed;
}
