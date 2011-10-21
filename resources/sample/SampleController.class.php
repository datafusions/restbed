<?php
/**
 * Description of SampleController
 * @file 
 * @author erwan
 * @date 05/05/2010
 */
use restbed\include\Controller;

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
        //return ResourceLoader::load('Sample', $id);
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
        $stuff = $this->requestInfo->getRequestData();

  //      $sample = ResourceLoader::create('Sample', $stuff);
        // Set the response header accordingly.?
        
        return $sample;
    }

    /**
     * @RB_Control(rmethod="POST", pattern="$id")
     */
    public function post(
    ) {
        $stuff = $this->requestInfo->getRequestData();
    //    $sample = ResourceLoader::load('Sample', $stuff);
        if ($sample == null) { // null means nothing found.
            return null;
        }
        
      //  ResourceLoader::update($sample, $stuff);

        return $sample;
    }
}
?>
