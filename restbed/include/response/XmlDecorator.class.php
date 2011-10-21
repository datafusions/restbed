<?php
/**
 * @brief This class turn a DataNode tree into an Xml Block.
 * @file include/response/XmlDecorator.class.php
 * @author erwan
 * @date 01/04/2010
 */
namespace restbed\response;

require_once('NodeParser.class.php');
use restbed\response\NodeParser;

class XmlDecorator {

    private $xmlDoc;

    public function __construct(
    ) {
        $this->xmlDoc = new \DOMDocument();
    }

    /**
     *
     *
     * @param ResponseBlock $object The object to decorate
     * @return String   XML string
     */
    public function decorate(
        ResponseBlock $object
    ) {
        $nodeParser = new NodeParser();
        $data = $nodeParser->parseBlock($object);
        $xmlElement = $this->decorateDataNodes($data);
        return $this->xmlDoc->saveXML($xmlElement);
    }

    /**
     *
     * @param DataNode $data The data to turn into a DOMElement.
     * @return DOMElement The DOMElement...
     */
    private function decorateDataNodes(
        DataNode $data
    ) {      
        $xmlRoot = $this->xmlDoc->createElement($this->xmlEntities($data->getName()));

        if ($data->getType() == DataNode::TYPE_SINGLE_VALUE) {
            $rootText = $this->xmlDoc->createTextNode($this->xmlEntities($data->getData()));
            $xmlRoot->appendChild($rootText);

            if (count($data->getAttributes()) > 0) {
                foreach($data->getAttributes() as $name => $value) {
                    $attrib = $this->xmlDoc->createAttribute($this->xmlEntities($name));
                    $attribVal = $this->xmlDoc->createTextNode($this->xmlEntities($value));
                    $attrib->appendChild($attribVal);

                    $xmlRoot->appendChild($attrib);
                }
            }
            
        } else if ($data->getType() == DataNode::TYPE_NODE) {

            if (is_array($data->getData())) {
                
                foreach ($data->getData() as $key => $datum) {
                    if ($datum instanceof DataNode) {
                        $xmlElement = $this->decorateDataNodes($datum);
                        if (count($datum->getAttributes()) > 0) {
                             foreach($datum->getAttributes() as $name => $value) {
                                $attrib = $this->xmlDoc->createAttribute($this->xmlEntities($name));
                                $attribVal = $this->xmlDoc->createTextNode($this->xmlEntities($value));
                                $attrib->appendChild($attribVal);
                                $xmlElement->appendChild($attrib);
                            }
                        }

                    } else {
                        if ($data->getDataKey() == '') {
                            if (is_numeric($key)) {
                                $key = '_'.$key;
                            }
                        } else {
                            $key = $data->getDataKey();
                        }
                        $xmlElement = $this->xmlDoc->createElement($key, $datum);
                    }
                    $xmlRoot->appendChild($xmlElement);
                }

            } else {
                $xmlElement = $this->xmlDoc->createElement($this->xmlEntities($data->getName()), $this->xmlEntities($data->getData()));
                
                if (count($data->getAttributes()) > 0) {
                     foreach($data->getAttributes() as $name => $value) {
                        $attrib = $this->xmlDoc->createAttribute($this->xmlEntities($name));
                        $attribVal = $this->xmlDoc->createTextNode($this->xmlEntities($value));
                        $attrib->appendChild($attribVal);

                        $xmlElement->appendChild($attrib);
                    }       
                }

                $xmlRoot->appendChild($xmlElement);
            }

            // Root Attributes...
            if (count($data->getAttributes()) > 0) {
                 foreach($data->getAttributes() as $name => $value) {
                    $attrib = $this->xmlDoc->createAttribute($this->xmlEntities($name));
                    $attribVal = $this->xmlDoc->createTextNode($this->xmlEntities($value));
                    $attrib->appendChild($attribVal);

                    $xmlRoot->appendChild($attrib);
                }
            }
        }

        return $xmlRoot;
    }

    // TAKEN FROM php.net : http://au.php.net/manual/en/function.htmlentities.php#99984
    private function xmlEntityDecode($text, $charset = 'UTF-8'){
        // Double decode, so if the value was &amp;trade; it will become Trademark
        $text = html_entity_decode($text, ENT_COMPAT, $charset);
        $text = html_entity_decode($text, ENT_COMPAT, $charset);
        return $text;
    }

    private function xmlEntities($text, $charset = 'UTF-8'){
        // Debug and Test
        // $text = "test &amp; &trade; &amp;trade; abc &reg; &amp;reg; &#45;";

        // First we encode html characters that are also invalid in xml
        $text = htmlentities($text, ENT_COMPAT, $charset, false);

        // XML character entity array from Wiki
        // Note: &apos; is useless in UTF-8 or in UTF-16
        $arr_xml_special_char = array("&quot;","&amp;","&apos;","&lt;","&gt;");

        // Building the regex string to exclude all strings with xml special char
        $arr_xml_special_char_regex = "(?";
        foreach($arr_xml_special_char as $key => $value){
            $arr_xml_special_char_regex .= "(?!$value)";
        }
        $arr_xml_special_char_regex .= ")";

        // Scan the array for &something_not_xml; syntax
        $pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

        // Replace the &something_not_xml; with &amp;something_not_xml;
        $replacement = '&amp;${1}';
        return preg_replace($pattern, $replacement, $text);
    }
}
?>
