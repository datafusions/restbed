<?php
/**
 * Description of DbException
 * @file include/db/DbException.class.php
 * @author Erwan Varaine
 * @date 2010-03-18
 *
 * @brief Exception used when SQL query fails.
 */

namespace restbed\db;

class DbException extends \Exception {

    private $query;     ///< The query that caused the exception.

    /**
     * Standard constructor business.
     *
     * @param String $message   The MySQL error string
     * @param int $code         The MySQL error code.
     * @param String $query     The query that caused the exception.
     */
    public function __construct(
        $message,
        $code,
        $query
    ) {
        parent::__construct($message, $code);
        $this->query = $query;

        error_log("DbException : $message");
    }

    /**
     * Get the query that caused the exception.
     *
     * @return String The Query.
     */
    public function getQuery() {
        return $this->query;
    }
        
}
?>
