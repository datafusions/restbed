<?php
/**
 * @brief This is the base RestBed Resource class, all resource in RestBed should extend this class.
 * @file include/resource/Resource.class.php
 * @author erwan varaine
 * @date 24/04/2010
 */
namespace restbed\resource;

abstract class ResourceBase implements \restbed\response\ResponseBlock {

    private $uid;
    private $lastModified;
    private $uri;

    public function __construct(
        $uid,
        $lastModified = null
    ) {
        $this->uid = $uid;
        $this->lastModified = ($lastModified == false ? date('Y-m-d H:i:s') : $lastModified);
    }

    /** @RB_BlockAttribute(property="root", attribute="uid") */
    public function getUid() { return $this->uid; }

    
    public function getLastModified() { return $this->lastModified; }

    /** @RB_BlockAttribute(property="root", attribute="uri")
     *
     * @return Mixed The URI String, if this object's UID is > 0 (saved) otherwise false if this is an unsaved object.
     */
    public function getUri() {
        return ($this->uri == '' ? false : $this->uri);
    }

    /**
     * Set the URI.
     */
    public function setUri(
        $uri
    ) {
        $this->uri = $uri;
    }
    
}
?>
