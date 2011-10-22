<?
header("Content-Type:text/xml");
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

$resource = $_REQUEST_INFO->getResourceName();
$controller = $_RESOURCE[$resource]['controller'];

if (!isset($controller)) {

    $_RESPONSE->setResponseCode(Response::HTTP_NOT_FOUND);
    $_RESPONSE->addMessage(Response::HTTP_NOT_FOUND, 'Not Found');

} else {

    //Instantiate the Controller class.
    $control = new $controller($_REQUEST_INFO, $_RESPONSE, $_USER);

    $reflection = new ReflectionAnnotatedClass($control);

    $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    $match = false;

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
                            
                            // call the function and add the returned block to the RESPONSE.
                            $response = $method->invokeArgs($control, $args);

                            if ($response instanceof ResourceBase) {
                                // TODO : Conditional Get.
                                // $response-getLastModified()
                                $_RESPONSE->addHeader('Last-Modified', $response->getLastModified());
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

    if (!$match) {
        $_RESPONSE->setResponseCode(Response::HTTP_NOT_FOUND);
        $_RESPONSE->addMessage(Response::HTTP_NOT_FOUND, 'Not Found');
    }
}

$_RESPONSE->send();
?>
