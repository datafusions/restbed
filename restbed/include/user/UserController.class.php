<?php
/**
 * Description of UserController
 * @file include/user/UserController.class.php
 * @author erwan
 * @date 15/04/2010
 */
use restbed\Controller;
use restbed\user\User;

class UserController extends Controller {

    /**
     * @RB_Control(rmethod="GET", pattern="")
     */
    public function getLoggedInUser() {
        return User::getLoggedInUser();
    }

    /**
     * @RB_Control(rmethod="GET", pattern="$id")
     *
     * Handle request for a GET by user id.
     */
    public function getUser(
        $id
    ) {
        return User::loadByUid($id);
    }


    /**
     * @RB_Control(rmethod="GET", pattern="$id/pref/$prefId")
     */
    public function getUserPrefs(
        $id,
        $prefId
    ) {
        return 'NA';
    }

    /**
     * @RB_Control(rmethod="OPTIONS", pattern="")
     */
    public function handleOptions() {
        $allowed = array('GET', 'POST', 'HEAD');

        $this->response->addHeader('Allow', implode(', ', $allowed));
        $this->response->addHeader('Content-Length', '0');

        return true;
    }
}
?>
