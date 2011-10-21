<?
/**
 * @file include/RequestInfo.class.php
 * @class RequestInfo
 * @author Erwan Varaine
 * @date 2010-03-18
 * 
 * @brief Class to get the RequestInfo data and parse it.Singleton Utility Class.
 *
 */
namespace restbed;

/** @RB_MultiValueBlock
 *  @RB_BlockName("request_info")
 */
class RequestInfo implements \ArrayAccess, \Countable, response\ResponseBlock {
    
    static private $requestInfo;       ///< The Singleton Instance.

    /**
     * Get the Singleton Instance of this object.
     *
     * @return RequestInfo object.
     */
    public static function getInstance() {
        if (self::$requestInfo == false) {
            self::$requestInfo = new RequestInfo();
        }

        return self::$requestInfo;
    }

//----------------------------------------------------------

    private $pathData;      ///< Holds the parsed path info data.
    private $pathString = '';    ///< Hold the raw path info string (without leading '/')
    private $queryString = '';

    /**
     * Private constructor, takes the $_SERVER['REQUEST_URI'] string and parses it.
     *
     */
    private function __construct() {
        $whichOneToUse = 'REQUEST_URI';

        // The request uris will be something like.
        // /{resource}(/{id}(/{method/category}))?({query string})
        // or
        // /{resource}/search?{query parameters}
        //
        $this->pathString = $_SERVER[$whichOneToUse];

        //Get the query string out. If there is one.
        $qPos = strpos($this->pathString, '?');
        if ($qPos !== false) {
            $this->queryString = substr($this->pathString, $qPos+1);
            $this->pathString = substr($this->pathString, 0, $qPos);
        }
        
        // If the first character of the PATH_INFO is a '/', remove it.
        $this->pathString = ($this->pathString[0] == '/' ? substr($this->pathString, 1) : $this->pathString);

        // remove trailing '/' if present.
        if ($this->pathString[strlen($this->pathString)-1] == '/') {
            $this->pathString = substr($this->pathString, 0, -1);
        }

        // All that so when we explode it the first and last elements aren't empty.
        $this->pathData = explode('/', $this->pathString);
    }

    /**
     *
     * @return String The Path requested.
     */
    public function __toString() {
        return ($this->pathString == false ? '' : $this->pathString);
    }

    /** @RB_BlockProperty("request_method")
     *
     * @return String   The request method, ie. GET, HEAD, POST ...
     */
    public function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRequestData() {
        switch($this->getRequestMethod()) {
            case 'GET' : return $_GET; break;
            case 'POST' : return $_POST; break;
            case 'HEAD' : return $_GET; break;
            case 'OPTIONS' : return $_GET; break;
            case 'DELETE' : return false; break;
            case 'PUT' : return $this->getPutData(); break;
        }
    }

    private function getPutData() {
        $putdata = fopen("php://input", "r");
        $buffer = '';

        while ($data = fread($putdata, 1024)) {
            $buffer .= $data;
        }

        return $buffer;
    }

    /**
     * @RB_BlockProperty("resource_name");
     */
    public function getResourceName() {
        return $this[0];
    }

    /*
     * Implement the ArrayAcess Interface
     */

    /**
     * Offset to retrieve
     *
     * @param $offset The offset to get.
     *
     * @return The data at offset.
     */
    public function offsetGet($offset) {
        return isset($this->pathData[$offset]) ? $this->pathData[$offset] : null;
    }

    /**
     * Whether a offset exists
     *
     * @param $offset The array offset to check.
     *
     * @return Boolean True if Offset exists, false otherwise.
     */
    public function offsetExists($offset) {
        return isset($this->pathData[$offset]);
    }
    
    /**
     * Offset to unset
     * Not used since this is a Read Only array.
     */
    public function offsetUnset($offset) {
        // READ ONLY
    }

    /**
     * Offset to set
     * Not used since this is a Read Only array.
     */
    public function offsetSet($offset, $value) {
        //READ ONLY
    }

    /*
     * Implement the Countable Interface
     */

    public function count() {
        return count($this->pathData);
    }

    /** @RB_BlockProperty("raw") */
    public function getRawUri() {
        return $_SERVER[REQUEST_URI];
    }
    
    /** @RB_BlockProperty("path_string") */
    public function getPathString() {
        return $this->pathString;
    }
    
    /** @RB_BlockProperty("query_string") */
    public function getQueryString() {
        return $this->queryString;
    }

    /** @RB_BlockProperty("path_info") */
    public function getPathData() {
        return $this->pathData;
    }
    
} // end class RequestInfo
?>
