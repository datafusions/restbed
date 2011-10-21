<?php
/**
 * Description of Sample
 * @file 
 * @author erwan
 * @date 24/04/2010
 */
/**
 *
 * @RB_MultiValueBlock
 */

use restbed\include\resource\ResourceBase;
use restbed\include\db\Db;

class Sample extends ResourceBase {
    
    public static function create(
        $xmlObj
    ) {
        $db = Db::getInstance();
        $sql = "INSERT INTO `sample` (`name`, `number`) VALUES ('$name', '$number')";

        $db->query($sql);
        $uid = $db->insertedId();

        $data = array('uid' => $uid, 'name' => $name, 'number' => $number, 'last_modified' => date('Y-m-d H:i:s'));
        return new Sample($data);
    }

    public static function loadByUid(
        $uid
    ) {
        
    }

    private $name;
    private $number;

    public function __construct(
        $data
    ) {
        parent::__construct($data['uid'], $data['last_modified']);
        $this->name = $data['name'];
        $this->number = $data['number'];
    }  

    /** @RB_BlockProperty("name") */
    public function getName() { return $this->name; }

    /** @RB_BlockProperty("number") */
    public function getNumber() {return $this->number; }
}
?>
