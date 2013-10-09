<?
header("Content-Type:text/xml"); // This should depend on which Decorator is used.
require_once('restbed/restbed.inc.php');

use restbed\response\Response;
use restbed\response\ResponseBlock;
use restbed\response\ResourceBase;
use restbed\config\Config;

if (Config::USES_AUTH) {
    // Pre load some information.
    $_USER = restbed\user\User::getLoggedInUser();

    if ($_USER == null) {
        $_RESPONSE->setResponseCode(Response::HTTP_UNAUTHORIZED);
        $_RESPONSE->addMessage(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
        $_RESPONSE->send();
        exit();
    }

}

/*
A convention (limitation?) of restbed is that the first part is the resource name that is linked to the controller in resources.conf.php .
*/
$resource = $_REQUEST_INFO->getResourceName();
$controller = (isset($_RESOURCE[$resource]['controller']) ? $_RESOURCE[$resource]['controller'] : null);

if ($controller == null) {

    $_RESPONSE->setResponseCode(Response::HTTP_NOT_FOUND);
    $_RESPONSE->addMessage(Response::HTTP_NOT_FOUND, 'Not Found');

} else {

    //Instantiate the Controller class.
    $control = new $controller($_REQUEST_INFO, $_RESPONSE, $_USER);

    $reflection = new ReflectionAnnotatedClass($control);

    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    $match = false;
    $response = false;

    // TODO : Add a caching mechanism so we don't iterate through a Controller's methods every time.
    // First check in the cache.
    if ($_CACHE != null) {
        $match = $_CACHE->invoke($_REQUEST_INFO, $control, $response);
    }

    if (!$match) {

        foreach($methods as $method) {
            $methodName = $method->getName();

            //error_log("METHOD : $methodName");
            if ($method->hasAnnotation('RB_Control')) {
                $requestMethod = $method->getAnnotation('RB_Control')->rmethod;

                if ($requestMethod == $_REQUEST_INFO->getRequestMethod()) {
                    $aPattern = array();

                    $pattern = $method->getAnnotation('RB_Control')->pattern;
                    if ($pattern != '') {
                        $aPattern = explode('/', $pattern);
                    }

                    $patternNum = count($aPattern);
                    //error_log("RI_C(".count($_REQUEST_INFO).") :: PNUM($patternNum) :: pattern(".implode(',', $aPattern).')');

                    // check we have the same amount of levels...
                    if (count($_REQUEST_INFO)-1 == $patternNum) {
                        $match = true;
                        $args = array();

                        if ($patternNum > 0) {
                            for ($i = 0 ; $i < $patternNum; ++$i) {
                                if ($aPattern[$i][0] == '$') {
                                    $args[] = $_REQUEST_INFO[$i+1];
                                } else if ($aPattern[$i] != $_REQUEST_INFO[$i+1]) {
                                    $match = false;
                                    break;
                                }
                            }
                        }

                        if ($match) {
                            //error_log("MATCH ON $methodName :: ARGS".implode(',',$args).')');
                            
                            // Store the match in the cache object.
                            if ($_CACHE != null) {
                                $_CACHE->store($_REQUEST_INFO, $method);
                            }
                            
                            // call the function and add the returned block to the RESPONSE.
                            $response = $method->invokeArgs($control, $args);

                            if ($response instanceof ResourceBase) {
                                // TODO : Conditional Get.

                                if ($response-getLastModified() != null) {
                                    $_RESPONSE->addHeader('Last-Modified', $response->getLastModified());
                                }

                                $_RESPONSE->addBlock($response);
                            } else if ($response instanceof ResponseBlock) {
                                $_RESPONSE->addBlock($response);
                            } else if ($response === true) {
                                // ....
                            } else if ($response != '') {
                                $_RESPONSE->addMessage("Response", $response);
                            } else if ($response === null) {
                                // Return null, when the uri pattern matches but nothing was found.
                                $match = false;
                            } else if ($response === false) {
                                // Return false, when user not allowed to perform method.
                                $match = true;
                                // Permission Denied scenario.
                            }                             

                            break;
                        }
                    }
                }
            }
        }
    }
    
    // If we still can't match, return a 404.
    if (!$match) {
        $_RESPONSE->setResponseCode(Response::HTTP_NOT_FOUND);
        $_RESPONSE->addMessage(Response::HTTP_NOT_FOUND, 'Not Found');
    }
}

$_RESPONSE->send();
?>
