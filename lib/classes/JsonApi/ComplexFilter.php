<?php
namespace JsonApi;

/**
 * @see https://www.jsonapi.net/usage/reading/filtering.html
 */
final class ComplexFilter
{
    private const OPERATIONS = [
        'equals' => [1, '%s = %s'],
        'lessThan' => [1, '%s < %s'],
        'lessOrEqual' => [1, '%s <= %s'],
        'greaterThan' => [1, '%s > %s'],
        'greaterOrEqual' => [1, '%s >= %s'],
        'contains' => [1, "%s LIKE CONCAT('%%', %s, '%%')"],
        'startsWith' => [1, "%s LIKE CONCAT(%s, '%%')"],
        'endsWith' => [1, "%s LIKE CONCAT('%%', %s)"],
        'any' => [-1, '%s IN (%s)'],
        //        'has',
        //        'not',
        //        'or',
        //        'and',
        'between' => [2, '%s BETWEEN %s AND %s'],
    ];

    public static function detect(string $input): bool
    {
        return preg_match(self::getRegexp(), $input);
    }

    public static function create(string $input): ComplexFilter
    {
        return new self($input);
    }

    public static function getRegexp(bool $withCaptureGroups = false): string
    {
        $quotedOperations  = array_map(
            function (string $operation): string {
                return preg_quote($operation, '/');
            },
            array_keys(self::OPERATIONS)
        );
        $template = $withCaptureGroups
            ? '/^(%s)\((\w+(?:,\w+)*)\)$/'
            : '/^(?:%s)\(\w+(?:,\w+)*\)$/';

        return sprintf($template, implode('|', $quotedOperations));
    }

    private $operation;
    private $parameters;

    private function __construct(string $input)
    {
        [$this->operation, $this->parameters] = $this->parse($input);
    }

    private function parse(string $input): array
    {
        preg_match(self::getRegexp(true), $input, $matches);

        $operation = $matches[1];
        $parameters = explode(',', $matches[2], self::OPERATIONS[$operation][0]);

        return [$operation, $parameters];
    }

    public function apply(array &$conditions, array &$parameters, string $column, string $variable = null): void
    {
        if ($variable === null) {
            $variable = ":${column}";
        }

        if (self::OPERATIONS[$this->operation][0] > 1) {
            $params = array_combine(
                array_map(
                    function ($index) use ($variable): string {
                        return "{$variable}{$index}";
                    },
                    array_keys($this->parameters)
                ),
                $this->parameters
            );
            $conditions[] = sprintf(
                self::OPERATIONS[$this->operation][1],
                $column,
                ...array_keys($params)
            );
            $parameters = array_merge(
                $parameters,
                $params
            );
        } elseif (self::OPERATIONS[$this->operation][0] === 1) {
            $conditions[] = sprintf(
                self::OPERATIONS[$this->operation][1],
                $column,
                $variable
            );
            $parameters[$variable] = $this->parameters[0];
        } elseif (self::OPERATIONS[$this->operation][0] === -1) {
            $conditions[] = sprintf(
                self::OPERATIONS[$this->operation][1],
                $column,
                $variable
            );
            $parameters[$variable] = $this->parameters;
        }
    }
}
