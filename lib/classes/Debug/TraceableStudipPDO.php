<?php
namespace Studip\Debug;

use DebugBar\DataCollector\PDO\TraceablePDO;

final class TraceableStudipPDO extends TraceablePDO
{
    /**
     * Quotes a string for use in a query.
     *
     * @link   http://php.net/manual/en/pdo.quote.php
     * @param  string $string The string to be quoted.
     * @param  int    $parameter_type [optional] Provides a data type hint for drivers that have
     * alternate quoting styles.
     * @return string|bool A quoted string that is theoretically safe to pass into an SQL statement.
     * Returns FALSE if the driver does not support quoting in this way.
     */
    #[\ReturnTypeWillChange]
    public function quote($string, $parameter_type = null)
    {
        return $this->pdo->quote($string, $parameter_type);
    }

}
