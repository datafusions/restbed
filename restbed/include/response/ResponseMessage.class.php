<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace restbed\response;

/** 
 * @RB_SingleValueBlock
 * @RB_BlockName("message")
 *
 * @file include/response/ResponseMessage.class.php
 * @author erwan
 * @date 18/03/2010
 */
class ResponseMessage implements ResponseBlock {
    
    private $message;   ///< The message
    private $type;      ///< The message type
    
    public function __construct(
        $message,
        $type = 'log'
    ) {
        $this->message = $message;
        $this->type = $type;
    }

    /** @RB_BlockContent
     *
     * @return String The Message
     */
    public function getMessage() {
        return $this->message;
    }

    /** @RB_BlockAttribute(property="root", attribute="type")
     *
     * @return String The Message Type
     */
    public function getType() {
        return $this->type;
    }
}
?>
