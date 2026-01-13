<?php
/**
 * This class is to be used in combination with SQLQuery if you need to
 * combine many SQLQuery objets into one single query using UNION.
 */
class SQLUnionQuery
{
    /**
     * @var SQLQuery[]
     */
    protected array $queries;
    protected bool $unionAll = false;

    public function __construct(SQLQuery ...$queries)
    {
        $this->queries = $queries;
    }

    /**
     * Adds a query to the union.
     */
    public function add(SQLQuery $query): void
    {
        $this->queries[] = $query;
    }

    /**
     * Whether UNION ALL should be used or not
     */
    public function setUnionAll(bool $unionAll): void
    {
        $this->unionAll = $unionAll;
    }

    /**
     * Returns the combined query
     */
    public function getQuery(): string
    {
        $queries    = [];
        foreach ($this->queries as $query) {
            $queries[] = $query->show();
        }

        $query = implode(
            $this->unionAll ? ' UNION ALL ' : ' UNION ',
            $queries
        );

        return $query;
    }

    /**
     * Returns the used parameters
     */
    public function getParameters(): array
    {
        $parameters = [];
        foreach ($this->queries as $query) {
            $parameters = array_merge($parameters, $query->settings['parameter'] ?? []);
        }

        return $parameters;
    }

    /**
     * Fetches all rows from the combined query
     */
    public function fetchAll(?callable $callable = null): array
    {
        return DBManager::get()->fetchAll(
            $this->getQuery(),
            $this->getParameters(),
            $callable
        );
    }

    /**
     * Fetches a single column from all rows of the combined query
     */
    public function fetchFirst(int $columnNumber = 0): array
    {
        return $this->fetchAll(function ($row) use ($columnNumber) {
            return array_values($row)[$columnNumber];
        });
    }
}
