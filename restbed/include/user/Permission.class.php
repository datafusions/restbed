<?php
/**
 * Description of Permission
 * @file include/user/Permission.class.php
 * @author Erwan Varaine
 * @date 20100319
 */
require_once('User.class.php');

class Permission {

    private $user; ///< The user who's permissions these are.

    private $permissions;

    const RESOURCE_USER = 'user';
    const RESOURCE_PREF = 'pref';
    const RESOURCE_SHOP = 'shop';


    const ACTION_U_V = 1;
    const ACTION_U_E = 2;
    const ACTION_U_A = 4;
    const ACTION_U_D = 8;

    const ACTION_G_V = 16;
    const ACTION_G_E = 32;
    const ACTION_G_A = 64;
    const ACTION_G_D = 128;

    const ACTION_O_V = 256;
    const ACTION_O_E = 512;
    const ACTION_O_A = 1024;
    const ACTION_O_D = 2048;

    /**
     * Create the permission object using a User object.
     * And get the permissions from the database.
     *
     * @param User $user The user who's permissions we are loading.
     */
    public function __construct(
        User $user
    ) {
        $this->user = $user;
        $db = Db::getInstance();

        $query = "SELECT * FROM user_permissions WHERE uuid = ".$user->getUid();

        $res = $db->query($query);
        if ($db->numRows($res) != 1) {
            $db->freeResult($res);
            throw new DbException("Too Many Results found", '0', $query);
        }

        $row = $db->fetchAssoc($res);
        $db->freeResult($res);

        foreach($row as $field=>$value) {
            if ($field == 'uuid') {
                continue;
            }
            $this->permissions[$field] = $value;
        }
    }

    /**
     * Can the user perform the $action on $resource
     *
     * @param const $resource   One of the RESOURCE_* constants.
     * @param const $action     One of the ACTION_* constants.
     *
     * @return mixed Boolean true if user can, false if can't, null if resource/action doesn't exist.
     */
    public function can(
        $resource,
        $action
    ) {
        if (!isset($this->permissions[$resource])
            || $action < 0
            || $action > 4095
           )
        {
            return null;
        }

        $uPerm = $this->permissions[$resource];

        if ( ($uPerm & $action) != 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>