<?php
/**
 * Description of Controller
 * @file include/Controller.class.php
 * @author erwan
 * @date 15/04/2010
 */
namespace restbed;

use restbed\response\Response;
use restbed\user\User;
use restbed\config\Config;
use restbed\resource\ResourceBase;

class Controller {

    protected $response;
    protected $user;
    protected $requestInfo;

    /**
     * Constructor
     *
     * Called from index.php, initialises requires framework objects.
     *
     * @param RequestInfo $requestInfo      The HTTP request object.
     * @param Response  $response           The Response object to (optionally) use?.
     * @param User      $user               The logged in user object if using AUTH, null otherwise.
     */
    public function __construct(
        RequestInfo $requestInfo,
        Response $response,
        User $user = null
    ) {
        $this->response = $response;
        $this->user = $user;
        $this->requestInfo = $requestInfo;

        $this->init();
    }
    
    /**
     * Init function to be overridden by extending Controllers.
     * Called at end of contructor.
     */
    protected function init() {

    }
    
    /**
     * Util function to set the URI of a Resource object.
     *
     * @param ResourceBase  $resource       The resource to modify
     * @param String        $function       The function string to add.
     */
    protected function setResourceUri(
        ResourceBase $resource,
        $function
    ) {
        $resource->setUri($this->makeResourceUri($function));
    }

    protected function makeResourceUri(
        $function
    ) {
        return Config::getUriBase().$function;
    }

}
?>
