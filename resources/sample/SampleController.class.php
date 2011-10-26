<?php
/**
 * Description of SampleController
 * @file 
 * @author erwan
 * @date 05/05/2010
 */
use restbed\Controller;
use restbed\response\Response;

require_once('Sample.class.php');
require_once('TestModel.class.php');

class SampleController extends Controller {

    /**
     * @RB_Control(rmethod="GET", pattern="$id")
     *
     * Handle request for a GET by sample id.
     */
    public function getSample(
        $id
    ) {
        return Sample::loadByUid($id);
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

    /**
     * @RB_Control(rmethod="PUT", pattern="")
     */
    public function put(
    ) {
        $data = $this->requestInfo->getRequestData();

        $this->response->setResponseCode(Response::HTTP_NOT_IMPLEMENTED);

        return false;
    }

    /**
     * @RB_Control(rmethod="POST", pattern="$id")
     */
    public function post(
    ) {
        $data = $this->requestInfo->getRequestData();

        $this->response->setResponseCode(Response::HTTP_NOT_IMPLEMENTED);
        return false;
    }

    /**
     * @RB_Control(rmethod="GET", pattern="example/$uid/with/$text")
     */
    public function example(
        $uid,
        $text
    ) {
        return new TestModel($uid, $text);
    }
}
?>
