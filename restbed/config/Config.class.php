<?php
/**
 * Config class, this is a singleton to store Static config data.
 * As well as utility config functions
 *
 * @author erwan
 * @date 2010-11-03
 */
namespace restbed\config;
class Config {

    //User editable configs

    // Location of the service.
    const PROTOCOL = 'http';
    const HOST = 'restbed.shacknet.nu';
    const PORT = '';
    const BASE_PATH = '';

    // User config
    const USES_AUTH = false; 			///< Use Authentication
    const AUTH_TABLE = 'users';			///< The database table containing user authentication
    const AUTH_FIELD_UID = 'uid';		///< The UID field for the user.
    const AUTH_FIELD_LOGIN = 'email';		///< The username field.
    const AUTH_FIELD_PASSWORD = 'password';	///< The password field.

    // Db Config
    const DB_HOST = 'localhost';    ///< Host of the mysql database
    const DB_USER = 'restbed';    ///< Username to connect to database
    const DB_PASSWORD = 'restbed'; ///< Password for above.
    const DB_DB = 'restbed';      ///< The database name

    // Directories
    const ROOT_DIR = '/var/www/restbed';
    const RESOURCE_DIR = 'resources/';
    const RESOURCE_FILE = 'restbed/config/resources.conf.php';

    // Responses
    const ENVELOPE_TAG = 'restbed';

    //-------------------------------------------------------------

    /**
     * @var Config The singleton instance
     */
    static private $configInstance;

    /**
     * Get the singleton instance of the Config class.
     *
     * @return Config singleton
     */
    public static function getInstance() {
        if (!self::$configInstance instanceof Config) {
            self::$configInstance = new Config();
        }

        return self::$configInstance;
    }

    /**
     * Constructor. Private.
     */
    private function __construct() {
    }

    /**
     * Get the service's base URI based on config values. includes trailing '/'     *
     *
     * @return <type> The service's base URI
     */
    public static function getUriBase() {
        return Config::PROTOCOL.'://'.Config::HOST.(Config::PORT == '' ? '' : ':'.Config::PORT).'/'.(Config::BASE_PATH == '' ? '' : Config::BASE_PATH.'/');
    }
   
}
?>
