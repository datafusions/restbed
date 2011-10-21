<?php
/**
 * @brief This class is used as a node when parsing the annotated class into a neutral hyrachical representation before being decorated. 
 *
 *
 * @file include/response/DataNode.class.php
 * @author erwan
 * @date 02/04/2010
 */
namespace restbed\response;

class DataNode {


    const TYPE_ROOT = 0;
    const TYPE_NODE = 1;
    const TYPE_SINGLE_VALUE = 2;

    public static function makeSingleValueNode(
        $name,
        $data = ''
    ) {
        return new DataNode(DataNode::TYPE_SINGLE_VALUE, $name, $data);
    }

    public static function makeNode(
        $name,
        $data = ''
    ) {
        return new DataNode(DataNode::TYPE_NODE, $name, $data);
    }

    private $name;      ///< The Node's name.
    private $data;      ///< Array of other DataNodes. or content...
    private $type;      ///< Node type.
    private $dataKey;   ///< IF data is an array with numeric index, use this key as the xml key. (if one is set...)
    private $attributes; ///< Array of key/value pair attributes

    public function __construct(
        $type,
        $name,
        $data
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->data = $data;
    }

    public function getType() { return $this->type; }
    public function getName() { return $this->name; }
    public function getData() { return $this->data; }
    public function getDataKey() { return $this->dataKey; }
    public function getAttributes() { return $this->attributes; }

    /**
     * Set the array key for this node. (used
     * @param String $dataKey   The new key for the node.
     */
    public function setDataKey(
        $dataKey
    ) {
        $this->dataKey = $dataKey;
    }

    /**
     * Add a node to the this node.
     * If the node data is not an array, it will be made so, thereby losing its content.
     * 
     * @param DataNode $data
     */
    public function addData(
        DataNode &$data
    ) {
       if (!is_array($this->data)) {
           $this->data = array();
       }

       $this->data[] = $data;
    }

    /**
     * Set the DataNode content.
     * If an array, must be an array of DataNode objects, otherwise is content.
     *
     * @param $data   The new content of DataNodes.
     */
    public function setData(
        $data
    ) {
        $this->data = $data;
    }

    /**
     * Add an attribute to this Node.
     * 
     * @param String $key       The attribute key.
     * @param mixed  $value     The attribute value (int/string)
     */
    public function addAttribute(
        $key,
        $value
    ) {
        $this->attributes[$key] = $value;
    }
    
    /**
     * Get a child node with name $name.
     *
     * @param String $name  The name of the child node to find.
     *
     * @return DataNode     The DataNode if found, null otherwise.
     */
    public function &getDataNode(
        $name
    ) {
        if (!is_array($this->data))
            return null;

        $found = null;

        foreach($this->data as $data) {
            if (!$data instanceof DataNode)
                continue;

            if ($data->getName() == $name) {
                $found = $data;
                break;
            }
        }

        return $found;
    }
}
?>
