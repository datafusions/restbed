<?php
/**
 * This will load RB_ResourceBase objects, kinda like a factory wraper.
 *
 * TODO : Cache loaded objects. so we minimize DB interaction. (memcache?)
 *
 * For Future : This is where we could plug in the Oration when it is done.
 *
 * This is a singleton
 */
namespace restbed\resource;

class ResourceLoader {
    private static $instance;

    /**
     * This is a test function. Do not depend on this...
     */
    public static function loadByUid(
        $className,
        $uid
    ) {
        
        

    }

    
    /**
     * Get the singleton instance of this class
     *
     * @return RB_ResourceLoader instance
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new ResourceLoader();
        }

        return self::$instance;
    }
    
    /**
     * Private construtor. doesn't actually do anything
     */
    private function __construct() {
    
    }

    

}
?>
