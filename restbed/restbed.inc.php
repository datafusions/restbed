<?
/**
 * @file restbed.inc.php
 * @author Erwan Varaine
 * @date 2010-03-18
 *
 * @brief Include file that contains the required common includes.
 */
require_once('config/Config.class.php');
use restbed\config\Config;

require_once('include/Annotations.inc.php');

require_once('include/response/Response.class.php');

require_once('include/RequestInfo.class.php');
require_once('include/Controller.class.php');

require_once('include/db/Db.class.php');

require_once('include/resource/ResourceBase.abstract.php');


// User Resource
$_USER = null;
if (Config::USES_AUTH) {
    require_once('include/user/User.class.php');
    require_once('include/user/UserController.class.php');
//    require_once('user/UserPref.class.php'); //?
    $_RESOURCE['user']['controller'] = 'UserController';
}


$_REQUEST_INFO = restbed\RequestInfo::getInstance();
$_RESPONSE = restbed\response\Response::getInstance();

require_once('config/resources.conf.php');
?>
