<?php
/**
 * Migration.php - abstract base class for migrations
 *
 * This class serves as the abstract base class for all migrations.
 *
 * @author    Marcus Lunzenauer <mlunzena@uos.de>
 * @copyright 2007 - Marcus Lunzenauer <mlunzena@uos.de>
 * @license   GPL2 or any later version
 * @package   migrations
 */
abstract class Migration
{
    /**
     * use verbose output
     *
     * @var boolean
     */
    private $verbose;

    /**
     * Initalize a Migration object (optionally using verbose output).
     *
     * @param boolean $verbose verbose output (default FALSE)
     */
    public function __construct($verbose = false)
    {
        $this->setVerbose($verbose);
    }

    /**
     * Sets the verbose state of this migration.
     * @param boolean $state Verbosity state
     */
    public function setVerbose($state = true)
    {
        $this->verbose = (bool) $state;
    }

    /**
     * Abstract method describing this migration step.
     * This method should be implemented in a migration subclass.
     *
     * @return string migration description
     */
    public function description()
    {
        return '';
    }

    /**
     * Returns the name of the migration. If the migration is an anonymous
     * class, the the name is created from the filename. Otherwise, it's the
     * class name of the migration.
     *
     * @return string
     */
    public function getName(): string
    {
        $reflection = new ReflectionClass($this);
        if ($reflection->isAnonymous()) {
            $filename = basename($reflection->getFileName(), '.php');
            $name = implode(' ', array_slice(explode('_', $filename), 1));
            return ucfirst($name);
        }
        return static::class;
    }

    /**
     * Abstract method performing this migration step.
     * This method should be implemented in a migration subclass.
     */
    protected function up()
    {
    }

    /**
     * Abstract method reverting this migration step.
     * This method should be implemented in a migration subclass.
     */
    protected function down()
    {
    }

    /**
     * Perform or revert this migration, depending on the indicated direction.
     *
     * @param ?string $direction migration direction (either 'up' or 'down')
     */
    public function migrate($direction)
    {
        if (!in_array($direction, ['up', 'down'])) {
            return null;
        }

        $result = $this->$direction();

        // Reset SORM cache
        SimpleORMap::expireTableScheme();

        return $result;
    }

    /**
     * Print the given string (if verbose output is enabled).
     *
     * @param string $text text to print
     */
    protected function write($text = '')
    {
        if ($this->verbose) {
            echo "{$text}\n";
        }
    }

    /**
     * Print the given formatted string (if verbose output is enabled).
     * Output always includes the migration's class name.
     *
     * @param string $format,... printf-style format string and parameters
     */
    public function announce($format /* , ... */)
    {
        # format message
        $args = func_get_args();
        $message = vsprintf(array_shift($args), $args);

        $this->write($this->mark($message));
    }

    /**
     * Pads and highlights a given text to a specific length with the given
     * sign.
     *
     * @param string $text
     * @param string $sign
     */
    protected function mark($text, $sign = '=')
    {
        $text = trim($text);
        if ($text) {
            $text = " {$text} ";
        }
        return str_pad("{$sign}{$sign}{$text}", 79, $sign, STR_PAD_RIGHT);
     }
}
