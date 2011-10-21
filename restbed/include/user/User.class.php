<?php
/**
 * @file include/User.class.php
 * @class User
 * @author Erwan Varaine
 * @date 2010-03-18
 * 
 * @brief Class to encapsulate User info.
 *
 */
namespace restbed\user;

use restbed\resource\ResourceBase;
use restbed\db\Db;
use restbed\config\Config;

/**
 * @RB_MultiValueBlock
 * @RB_BlockName("user")
 */
class User extends ResourceBase {

    static private $loggedInUser;       ///< Contains the Singleton User object for the logged in user.

    /**
     * Get the currently logged in user.
     * This function only gets the user data from the database upon first load.
     * Seeing as a user interaction is pretty short this should be fine.
     *
     * @return User The loaded user object, null if none found.
     * @throws DbException on SQL error.
     */
    public static function getLoggedInUser() {
        if (self::$loggedInUser == false) {
            //We need the Db class to escape the username, just in case.
            $db = Db::getInstance();

            // The email is used as the username to log into the service.
            $login = $db->escapeString($_SERVER['PHP_AUTH_USER']);
            $password = $db->escapeString($_SERVER['PHP_AUTH_PW']);
            
            $query = "SELECT ".Config::AUTH_FIELD_UID.", ".Config::AUTH_FIELD_LOGIN." "
                    ."FROM ".Config::DB_DB.".".Config::AUTH_TABLE." WHERE "
                    .Config::AUTH_FIELD_LOGIN." = '$login' "
                    ."AND `".Config::AUTH_FIELD_PASSWORD."` = SHA1('$password')";

            self::$loggedInUser = self::loadBySql($query);
        }

        return self::$loggedInUser;
    }

    /**
     * Load a user object using the uid as key.
     *
     * @param int $uid  The uid to use.
     * @return User The loaded user object, null if none found.
     * @throws DbException on SQL error.
     */
    public static function loadByUid(
        $uid
    ) {
        return self::loadBySql("SELECT * FROM ".Config::DB_DB.".".Config::AUTH_TABLE." WHERE uid = '$uid'");
    }

    /**
     * Load a user object using a SQL query .
     * 
     * @param String $sql The SQL Query to use.
     * @throws DbException
     * @return User The loaded user object.
     */
    private static function loadBySql(
        $sql
    ) {
        $db = Db::getInstance();
        $res = $db->query($sql);

        if ($db->numRows($res) != 1) {
            return null;
        }

        $row = $db->fetchAssoc($res);
        $db->freeResult($res);

        return new User($row);
    }

//-----------------------------------------------------------------
    
    private $login;     ///< the login

    public function __construct(
        $data
    ) {
        parent::__construct($data[Config::AUTH_FIELD_UID], $data['last_modified']);
        $this->login = $data[Config::AUTH_FIELD_LOGIN];
    }

    /** @RB_BlockProperty("login")
     *
     * @return String The user's login.
     */
    public function getLogin() {
        return $this->login;
    }

    /** @RB_BlockAttribute(property="root", attribute="uri")
     *
     * @return Mixed The URI String, if this object's UID is > 0 (saved) otherwise false if this is an unsaved object.
     */
    public function getUri() {
        if ($this->getUid() > 0)
            return Config::getUriBase().'user/'.$this->getUid();
        else
            return false;
    }
}
?>
