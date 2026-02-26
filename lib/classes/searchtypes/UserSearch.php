<?php


/**
 * The UserSearch search type can search user accounts using different criteria
 * and filters.
 */
class UserSearch extends SQLSearch
{
    /**
     * @var SimpleORMap[] The objects for filtering user accounts.
     * The user accounts must be associated to the objects in this list
     * to be included in the result set.
     */
    protected array $object_filters = [];

    public function __construct(
        string $title = '',
        string $field_name = 'user_id'
    )
    {
        $this->title = $title ?? _('Person suchen');
        $this->avatarLike = 'user_id';
    }

    /**
     * Adds a SimpleORMap object as filter. The user account must be associated
     * to the object in order to be included in the result set.
     *
     * @param SimpleORMap $object The object to be used as filter.
     *
     * @return bool True, if the object can be used as filter, false otherwise.
     */
    public function addObjectAsFilter(SimpleORMap $object) : bool
    {
        if ($object instanceof Institute) {
            //TODO: activate JOIN institute
        } elseif ($object instanceof Course) {
            //TODO: activate JOIN seminar_user
        } else {
            //Unsupported object.
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getResults($input, $contextual_data, $limit = PHP_INT_MAX, $offset = 0)
    {

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

    }
}
