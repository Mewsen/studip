<?php


/**
 * The UserSearch search type can search user accounts using different criteria
 * and filters.
 */
class UserSearch extends SQLSearch
{
    /**
     * @var string[] The institute IDs for filtering user accounts.
     * The users must be members of the institutes in this list
     * to be included in the result set.
     */
    protected array $institute_filters = [];

    /**
     * @var string[] The course IDs for filtering user accounts.
     * The users must be members
     */
    protected array $course_filters = [];

    /**
     * @var string The SQL query string for the getResults or search method.
     */
    protected string $sql_query = '';

    /**
     * @var array The SQL query parameters for the SQL query string in $sql_query.
     */
    protected array $sql_params = [];

    public function __construct(
        string $title = ''
    )
    {
        $this->title = $title ?? _('Person suchen');
        $this->avatarLike = 'user_id';
    }

    /**
     * Adds a filter as a condition.
     *
     * @param  The condition to add as user account filter.
     */
    public function addInstituteFilter(string $institute_id)
    {
        $this->institute_filters[] = $institute_id;
    }

    /**
     * This is a helper method to generate the SQL query and its parameters for the
     * getResults or search method. The sql_query and sql_params attributes are set
     * by this method.
     *
     * @return void
     */
    protected function prepareQuery() : void
    {

    }

    /**
     * @inheritDoc
     */
    public function getResults($input, $contextual_data = [], $limit = PHP_INT_MAX, $offset = 0)
    {
        $this->limit  = $limit;
        $this->offset = $offset;

        $this->prepareQuery();
        if (!$this->sql_query) {
            //No query string has been generated.
            return [];
        }
        $db = DBManager::get();
        $stmt = $db->prepare($this->sql_query);
        $stmt->execute($this->sql_params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Searches for user accounts and returns User objects instead of an associative array.
     *
     * @param string $search_keyword The keyword(s) for finding user accounts.
     *
     * @param int $limit The maximum amount of user accounts to find.
     *
     * @param int $offset The offset from which to start finding user accounts.
     *
     * @return User[] An array with found user accounts.
     */
    public function search(string $search_keyword, int $limit = PHP_INT_MAX, int $offset = 0) : array
    {
        $this->limit  = $limit;
        $this->offset = $offset;

        $this->prepareQuery();
        if (!$this->sql_query) {
            //No query string has been generated.
            return [];
        }

        return User::findBySQL($this->sql_query, $this->sql_query_params);
    }
}
