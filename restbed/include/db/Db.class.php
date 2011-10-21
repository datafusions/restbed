<?
/**
 * @file include/db/Db.class.php
 * @class Db
 * @author Erwan Varaine
 * @date 2010-03-18
 * 
 * @brief Singletone class to access the database. (MySQL)
 *
 */
namespace restbed\db;

require_once('DbException.class.php');
use restbed\db\DbException;
use restbed\config\Config;

class Db {

    private static $dbInstance;

    public function getInstance() {
        if (self::$dbInstance == false) {
            self::$dbInstance = new Db();
        }

        return self::$dbInstance;
    }

//-----------------------------------------------------------------

    private $connection = false;    ///< Holds the connection resource

    /**
     * Private constructor, lazy connect so we don't connect here.
     * Doesn't really do much...
     */
    private function __construct() {

    }

    /**
     * Connect to the MySQL server.
     *
     * @throws DbException On failure.
     */
    private function connect() {
        
        $this->connection = mysql_connect(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD);
        
        if ($this->connection == false) {
            throw new DbException(mysql_error(), mysql_errno(), "CONNECTION");
        }
        
        mysql_select_db(Config::DB_DB, $this->connection);
    }

    /**
     * Run the query, this is a lazy connect class, so if a connection hasn't being established, it will now.
     *
     * @param String $query The query to run.
     *
     * @return Mixed The ResultSet resource returned by mysql_query()
     * @throws DbException On SQL Error.
     */
    public function query(
        $query
    ) {
        if ($this->connection == false) {
            $this->connect();
        }

        $res = mysql_query($query, $this->connection);
        if (mysql_errno()) {
            throw new DbException(mysql_error(), mysql_errno(), $query);
        }

        return $res;
    }

    public function numRows(
        $result
    ) {
        return mysql_num_rows($result);
    }

    public function fetchAssoc(
        $result
    ) {
        return mysql_fetch_assoc($result);
    }

    public function fetchArray(
        $result
    ) {
        return mysql_fetch_array($result);
    }

    public function freeResult(
        $result
    ) {
        return mysql_free_result($result);
    }

    public function escapeString(
        $string
    ) {
        if ($this->connection == false) {
            $this->connect();
        }

        return mysql_real_escape_string($string);
    }

    public function getInsertId() {
        return mysql_insert_id();
    }
}
?>
