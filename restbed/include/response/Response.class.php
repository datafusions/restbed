<?php
/**
 * Description of Response
 * @file include/Response.class.php
 * @author erwan
 * @date 2010-03-18
 *
 * @brief Class to create the response.
 */
namespace restbed\response;

require_once('ResponseBlock.interface.php');
require_once('ResponseMessage.class.php');
require_once('XmlDecorator.class.php');

use restbed\response\ResponseBlock;
use restbed\response\ResponseMessage;
use restbed\response\XmlDecorator;
use restbed\RequestInfo;
use restbed\config\Config;

class Response {

    // only the most used one here so far...

    //1xx
    const HTTP_CONTINUE = '100 Continue';   // Continue is a reserved word... This ain't nice.
    const HTTP_SWITCHING_PROTOCOL = '101 Switching Protocols';
    const HTTP_WEBDAV_PROCESSING = '102 Processing'; // (WebDAV) (RFC 2518)
    const HTTP_CHECKPOINT = '103 Checkpoint';
    
    //2xx
    const HTTP_OK = '200 OK';
    const HTTP_CREATED = '201 Created';
    const HTTP_ACCEPTED = '202 Accepted';
    const HTTP_NON_AUTHORITATIVE_INFORMATION = '203 Non-Authoritative Information'; // (since HTTP/1.1)
    const HTTP_NO_CONTENT = '204 No Content';
    const HTTP_RESET_CONTENT = '205 Reset Content';
    const HTTP_PARTIAL_CONTENT = '206 Partial Content';
    const HTTP_WEBDAV_MULTI_STATUS = '207 Multi-Status'; // (WebDAV) (RFC 4918)

    //3xx
    const HTTP_MOVED_PERMANENTLY = '301 Moved Permanently';
    const HTTP_MOVED_TEMPORARILY = '302 Moved Temporarily';
    const HTTP_SEE_OTHER = '303 See Other';
    const HTTP_NOT_MODIFIED = '304 Not Modified';
    const HTTP_TEMPORARY_REDIRECT = '307 Temporary Redirect';
    const HTTP_RESUME_INCOMPLETE = '308 Resume Incomplete';

    //4xx
    const HTTP_BAD_REQUEST = '400 Bad Request';
    const HTTP_UNAUTHORIZED = '401 Unauthorized';
    const HTTP_FORBIDDEN = '403 Forbidden';
    const HTTP_NOT_FOUND = '404 Not Found';
    const HTTP_METHOD_NOT_ALLOWED = '405 Method Not Allowed';
    const HTTP_NOT_ACCEPTABLE = '406 Not Acceptable';
    const HTTP_REQUEST_TIMEOUT = '408 Request Timeout';
    const HTTP_CONFLICT = '409 Conflict';
    const HTTP_LENGTH_REQUIRED = '411 Length Required';
    const HTTP_I_M_A_TEAPOT = '418 I\'m a teapot'; // (RFC 2324)

    //5xx
    const HTTP_INTERNAL_SERVER_ERROR = '500 Internal Server Error';
    const HTTP_NOT_IMPLEMENTED = '501 Not Implemented';
    const HTTP_SERVICE_UNAVAILABLE = '503 Service Unavailable';
    const HTTP_GATEWAY_TIMEOUT = '504 Gateway Timeout';
    const HTTP_VERSION_NOT_SUPPORTED = '505 HTTP Version Not Supported';
    
    private static $responseInstance; ///< The response singleton instance.
    /**
     * Get the instance of this class. There can only be one Response per session.
     *
     * @return Response     The response object for the current session.
     */
    public static function getInstance() {
        if (self::$responseInstance == false) {
            self::$responseInstance = new Response();
        }

        return self::$responseInstance;
    }

    private $blocks; ///< Array of response block, in the order they will appear.
    private $responseCode;  ///< The response code.
    private $messages;  ///< Array of response messages.
    private $headers; //< Array of headers to send before output.

    /**
     * Basic Constructor.
     */
    private function __construct() {
        $this->blocks = array();
        $this->messages = array();
        $this->headers = array();
    }

    /**
     * Add a block to be add to the response.
     *
     * @param ResponseBlock $block The block to add to the response.
     */
    public function addBlock(
        ResponseBlock $block
    ) {
        $this->blocks[] = $block;
    }

    /**
     * Add a message to the response object.
     * @param string $message
     */
    public function addMessage(
        $message,
        $type = 'log'
    ) {
        $this->messages[] = new ResponseMessage($message, $type);
    }

    /**
     * Add header to be set before output.
     *
     * @param String $header A header type
     * @param String $value The header value.
     */
    public function addHeader(
        $header,
        $value
    ) {
        $this->headers[$header] = $value;
    }

    /**
     * Set the HTTP response code. This adds a 'Status' header.
     * TODO : Need to add a class with Response Codes...
     *
     * @param <type> $responseCode The response code + string. (ie "404 Not Found")
     */
    public function setResponseCode(
        $responseCode
    ) {
        $this->responseCode = $responseCode;
        $this->addHeader('Status', $responseCode);
    }

    /**
     * Send this response. This should only be done at the end of the session.
     * And only called once.
     *
     * This echos out...
     */
    public function send() {
        header("HTTP/1.0 ".$this->responseCode);

        foreach($this->headers as $header => $value) {
            header($header.': '.$value);
        }

        if (RequestInfo::getInstance()->getRequestMethod() != 'OPTIONS')
            echo $this->toString();
    }

    /**
     * The visual reprensentation of this object, in XML.
     *
     * @return String   The XML representation of this object.
     */
    public function toString() {
        $decorator = new XmlDecorator();

        $ret = '<'.Config::ENVELOPE_TAG.'>';

        if (count($this->messages) > 0) {
            $ret .= '<messages>';
            foreach($this->messages as $message) {
                try {
                    $ret .= $decorator->decorate($message);
                } catch (Exception $e) {
                    error_log("Exception Thrown : $e");
                }
            }
            $ret .= '</messages>';
        }

        foreach ($this->blocks as $block) {
            try {
                $ret .= $decorator->decorate($block);
            } catch (Exception $e) {
                error_log("Exception Thrown : $e");
            }
        }

        $ret .= '</'.Config::ENVELOPE_TAG.'>';

        return $ret;
    }

    public function __toString() { return $this->toString(); }
}
?>
