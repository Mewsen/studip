<?php
/*
 * Template.php - expression template parser
 *
 * Copyright (c) 2013  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

namespace exTpl;

use Closure;
use InvalidArgumentException;


/**
 * The Template class is the only externally visible API of this
 * template implementation. It can be used to create and render
 * template objects.
 */
class Template
{
    private static string $tag_start = '{';
    private static string $tag_end   = '}';
    private Closure|string|null $escape = null;
    private array $functions;
    private ArrayNode $template;

    /**
     * Sets the delimiter strings used for the template tags, the
     * default delimiters are: $tag_start = '{', $tag_end = '}'.
     *
     * @param string $tag_start tag start marker
     * @param string $tag_end tag end marker
     */
    public static function setTagMarkers(string $tag_start, string $tag_end): void
    {
        self::$tag_start = $tag_start;
        self::$tag_end   = $tag_end;
    }

    /**
     * Initializes a new Template instance from the given string.
     *
     * @param string $string template text
     *
     * @throws TemplateParserException
     */
    public function __construct(string $string)
    {
        $this->template  = new ArrayNode();
        $this->functions = [
            'count'  => fn($a) => count($a),
            'strlen' => fn($a) => mb_strlen($a),
        ];
        self::parseTemplate($this->template, $string, 0);
    }

    /**
     * Enables or disables automatic escaping for template values.
     * Currently supported strategies: NULL, 'html', 'json', 'xml'
     *
     * @param callable|string|null $escape escape strategy or callback
     */
    public function autoescape(callable|string|null $escape): void
    {
        if ($escape === 'html' || $escape === 'xml') {
            $this->escape = 'htmlspecialchars';
        } else if ($escape === 'json') {
            $this->escape = 'json_encode';
        } else if (is_callable($escape)) {
            $this->escape = $escape;
        } else if ($escape === null) {
            $this->escape = null;
        } else {
            throw new InvalidArgumentException("invalid escape strategy: $escape");
        }
    }

    /**
     * Renders the template to a string using the given array of
     * bindings to resolve symbol references inside the template.
     *
     * @param array $bindings symbol table
     *
     * @return string   string representation of the template
     */
    public function render(array $bindings): string
    {
        $context = new Context($bindings + $this->functions);
        $context->autoescape($this->escape);

        return $this->template->render($context);
    }

    /**
     * Skips tokens until the end of the current tag is reached.
     *
     * @param string $string template text
     * @param int $pos offset in string
     *
     * @return int new offset in the string
     */
    private static function skipTokens(string $string, int $pos): int
    {
        for ($len = strlen($string); $pos < $len && substr_compare($string, self::$tag_end, $pos, strlen(self::$tag_end)); ++$pos) {
            $chr = $string[$pos];
            if ($chr === '"' || $chr === "'") {
                while (++$pos < $len && $string[$pos] !== $chr) {
                    if ($string[$pos] === '\\') {
                        ++$pos;
                    }
                }
            }
        }

        return $pos;
    }

    /**
     * Parses a template string into a template node tree, starting
     * at the specified offset. All created nodes are added to the
     * given sequence node.
     *
     * @param ArrayNode $node   template node to build
     * @param string    $string string to parse
     * @param int       $pos    offset in string
     *
     * @return int      new offset in the string
     * @throws TemplateParserException
     */
    private static function parseTemplate(ArrayNode $node, string $string, int $pos): int
    {
        $len = strlen($string);

        while ($pos < $len) {
            $next_pos = strpos($string, self::$tag_start, $pos);

            if ($next_pos === false) {
                $child = new TextNode(substr($string, $pos));
                $node->addChild($child);
                break;
            }

            if ($next_pos > $pos) {
                $child = new TextNode(substr($string, $pos, $next_pos - $pos));
                $node->addChild($child);
            }

            $pos      = $next_pos + strlen(self::$tag_start);
            $next_pos = self::skipTokens($string, $pos);
            $scanner  = new Scanner(substr($string, $pos, $next_pos - $pos));
            $pos      = $next_pos + strlen(self::$tag_end);

            switch ($scanner->nextToken()) {
                case T_FOREACH:
                    $scanner->nextToken();
                    $expr     = self::parseExpr($scanner);
                    $key_name = 'index';
                    $val_name = 'this';

                    if ($scanner->tokenType() === T_AS) {
                        $scanner->nextToken();

                        if ($scanner->tokenType() !== T_STRING) {
                            throw new TemplateParserException('symbol expected', $scanner);
                        }

                        $val_name = $scanner->tokenValue();
                        $scanner->nextToken();

                        if ($scanner->tokenType() === T_DOUBLE_ARROW) {
                            $scanner->nextToken();

                            if ($scanner->tokenType() !== T_STRING) {
                                throw new TemplateParserException('symbol expected', $scanner);
                            }

                            $key_name = $val_name;
                            $val_name = $scanner->tokenValue();
                            $scanner->nextToken();
                        }
                    }

                    $child = new IteratorNode($expr, $key_name, $val_name);
                    $pos   = self::parseTemplate($child, $string, $pos);
                    $node->addChild($child);
                    break;
                case T_ENDIF:
                case T_ENDFOREACH:
                    return $pos;
                case T_IF:
                    $scanner->nextToken();
                    $child = new ConditionNode(self::parseExpr($scanner));
                    $pos   = self::parseTemplate($child, $string, $pos);
                    $node->addChild($child);
                    break;
                case T_ELSEIF:
                    $scanner->nextToken();
                    $child = new ConditionNode(self::parseExpr($scanner));
                    $node->addElse();
                    $node->addChild($child);
                    return self::parseTemplate($child, $string, $pos);
                case T_ELSE:
                    $scanner->nextToken();
                    $node->addElse();
                    break;
                default:
                    $child = new ExpressionNode(self::parseExpr($scanner));
                    $node->addChild($child);
            }

            if ($scanner->tokenType() !== false) {
                throw new TemplateParserException('syntax error', $scanner);
            }
        }

        return $pos;
    }

    /**
     * value: NUMBER | STRING | SYMBOL | '(' expr ')'
     *
     * @throws TemplateParserException
     */
    private static function parseValue(Scanner $scanner): mixed
    {
        switch ($scanner->tokenType()) {
            case T_CONSTANT_ENCAPSED_STRING:
            case T_DNUMBER:
            case T_LNUMBER:
                $result = new ConstantExpression($scanner->tokenValue());
                break;
            case T_STRING:
                $result = new SymbolExpression($scanner->tokenValue());
                break;
            case '(':
                $scanner->nextToken();
                $result = self::parseExpr($scanner);

                if ($scanner->tokenType() !== ')') {
                    throw new TemplateParserException('missing ")"', $scanner);
                }
                break;
            default:
                throw new TemplateParserException('syntax error', $scanner);
        }

        $scanner->nextToken();
        return $result;
    }

    /**
     * function: value | function '(' ')' | function '(' expr { ',' expr } ')'
     *
     * @throws TemplateParserException
     */
    private static function parseFunction(Scanner $scanner): mixed
    {
        $result = self::parseValue($scanner);
        $type   = $scanner->tokenType();

        while ($type === '(') {
            $scanner->nextToken();
            $arguments = [];

            if ($scanner->tokenType() !== ')') {
                $arguments[] = self::parseExpr($scanner);

                while ($scanner->tokenType() === ',') {
                    $scanner->nextToken();
                    $arguments[] = self::parseExpr($scanner);
                }

                if ($scanner->tokenType() !== ')') {
                    throw new TemplateParserException('missing ")"', $scanner);
                }
            }

            $scanner->nextToken();
            $result = new FunctionExpression($result, $arguments);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * index: function | index '[' expr ']' | index '.' SYMBOL
     *
     * @throws TemplateParserException
     */
    private static function parseIndex(Scanner $scanner): mixed
    {
        $result = self::parseFunction($scanner);
        $type   = $scanner->tokenType();

        while ($type === '[' || $type === '.') {
            $scanner->nextToken();

            if ($type === '[') {
                $expr = self::parseExpr($scanner);

                if ($scanner->tokenType() !== ']') {
                    throw new TemplateParserException('missing "]"', $scanner);
                }
            } else if ($scanner->tokenType() === T_STRING) {
                $expr = new ConstantExpression($scanner->tokenValue());
            } else {
                throw new TemplateParserException('symbol expected', $scanner);
            }

            $scanner->nextToken();
            $result = new IndexExpression($result, $expr, $type);
            $type = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * filter: index | filter '|' SYMBOL | filter '|' SYMBOL '(' expr { ',' expr } ')'
     *
     * @throws TemplateParserException
     */
    private static function parseFilter(Scanner $scanner): mixed
    {
        $result = self::parseIndex($scanner);
        $type   = $scanner->tokenType();

        while ($type === '|') {
            $scanner->nextToken();

            if ($scanner->tokenType() !== T_STRING) {
                throw new TemplateParserException('symbol expected', $scanner);
            }

            $arguments = [$result];
            $symbol    = new SymbolExpression($scanner->tokenValue());
            $scanner->nextToken();

            if ($scanner->tokenType() === '(') {
                $scanner->nextToken();

                if ($scanner->tokenType() !== ')') {
                    $arguments[] = self::parseExpr($scanner);

                    while ($scanner->tokenType() === ',') {
                        $scanner->nextToken();
                        $arguments[] = self::parseExpr($scanner);
                    }

                    if ($scanner->tokenType() !== ')') {
                        throw new TemplateParserException('missing ")"', $scanner);
                    }
                }

                $scanner->nextToken();
            }

            if ($symbol->name() === 'raw') {
                $result = new RawExpression($result);
            } else {
                $result = new FunctionExpression($symbol, $arguments);
            }

            $type = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * sign: '!' sign | '+' sign | '-' sign | filter
     *
     * @throws TemplateParserException
     */
    private static function parseSign(Scanner $scanner): mixed
    {
        switch ($scanner->tokenType()) {
            case '!':
                $scanner->nextToken();
                $result = new NotExpression(self::parseSign($scanner));
                break;
            case '+':
                $scanner->nextToken();
                $result = self::parseSign($scanner);
                break;
            case '-':
                $scanner->nextToken();
                $result = new MinusExpression(self::parseSign($scanner));
                break;
            default:
                $result = self::parseFilter($scanner);
        }

        return $result;
    }

    /**
     * product: sign | product '*' sign | product '/' sign | product '%' sign
     *
     * @throws TemplateParserException
     */
    private static function parseProduct(Scanner $scanner): mixed
    {
        $result = self::parseSign($scanner);
        $type   = $scanner->tokenType();

        while ($type === '*' || $type === '/' || $type === '%') {
            $scanner->nextToken();
            $result = new ArithExpression($result, self::parseSign($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * sum: product | sum '+' product | sum '-' product | sum '~' product
     *
     * @throws TemplateParserException
     */
    private static function parseSum(Scanner $scanner): mixed
    {
        $result = self::parseProduct($scanner);
        $type   = $scanner->tokenType();

        while ($type === '+' || $type === '-' || $type === '~') {
            $scanner->nextToken();
            $result = new ArithExpression($result, self::parseProduct($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * lt_gt: sum | lt_gt '<' concat | lt_gt IS_SMALLER_OR_EQUAL concat
     *            | lt_gt '>' concat | lt_gt IS_GREATER_OR_EQUAL concat
     *
     * @throws TemplateParserException
     */
    private static function parseLtGt(Scanner $scanner): mixed
    {
        $result = self::parseSum($scanner);
        $type   = $scanner->tokenType();

        while ($type === '<' || $type === T_IS_SMALLER_OR_EQUAL ||
            $type === '>' || $type === T_IS_GREATER_OR_EQUAL) {
            $scanner->nextToken();
            $result = new BooleanExpression($result, self::parseSum($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * cmp: lt_gt | cmp IS_EQUAL lt_gt | cmp IS_NOT_EQUAL lt_gt
     *
     * @throws TemplateParserException
     */
    private static function parseCmp(Scanner $scanner): mixed
    {
        $result = self::parseLtGt($scanner);
        $type   = $scanner->tokenType();

        while ($type === T_IS_EQUAL || $type === T_IS_NOT_EQUAL) {
            $scanner->nextToken();
            $result = new BooleanExpression($result, self::parseLtGt($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * and: cmp | and BOOLEAN_AND cmp
     *
     * @throws TemplateParserException
     */
    private static function parseAnd(Scanner $scanner): mixed
    {
        $result = self::parseCmp($scanner);
        $type   = $scanner->tokenType();

        while ($type === T_BOOLEAN_AND) {
            $scanner->nextToken();
            $result = new BooleanExpression($result, self::parseCmp($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * or: and | or BOOLEAN_OR and
     *
     * @throws TemplateParserException
     */
    private static function parseOr(Scanner $scanner): mixed
    {
        $result = self::parseAnd($scanner);
        $type   = $scanner->tokenType();

        while ($type === T_BOOLEAN_OR) {
            $scanner->nextToken();
            $result = new BooleanExpression($result, self::parseAnd($scanner), $type);
            $type   = $scanner->tokenType();
        }

        return $result;
    }

    /**
     * expr: or | or '?' expr ':' expr | or '?' ':' expr
     *
     * @throws TemplateParserException
     */
    private static function parseExpr(Scanner $scanner): mixed
    {
        $result = self::parseOr($scanner);

        if ($scanner->tokenType() === '?') {
            $scanner->nextToken();

            if ($scanner->tokenType() !== ':') {
                $expr = self::parseExpr($scanner);
            } else {
                $expr = $result;
            }

            if ($scanner->tokenType() !== ':') {
                throw new TemplateParserException('missing ":"', $scanner);
            }

            $scanner->nextToken();
            $result = new ConditionExpression($result, $expr, self::parseExpr($scanner));
        }

        return $result;
    }
}
