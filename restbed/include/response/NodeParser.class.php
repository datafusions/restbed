<?php
/**
 * Description of NodeParser
 * @file 
 * @author erwan
 * @date 15/04/2010
 */
namespace restbed\response;

require_once('DataNode.class.php');
use restbed\response\DataNode;

class NodeParser {

    /**
     *
     * @param ResponseBlock $object The object to parse.
     * @return DataNode The DataNode object representing the $object.
     */
    public function parseBlock(
        ResponseBlock $object
    ) {
        $reflection = new \ReflectionAnnotatedClass($object);

        if ($reflection->hasAnnotation('RB_BlockName')) {
            $className = $reflection->getAnnotation('RB_BlockName')->value;
        } else {
            $className = strtolower(get_class($object));
        }

//var_dump($reflection);

        if ($reflection->hasAnnotation('RB_SingleValueBlock')) {
            error_log('SINGLE');
            $data = $this->parseSingleValueBlock($object, $reflection, $className);
        } else if ($reflection->hasAnnotation('RB_MultiValueBlock')) {
            error_log('MULTI');
            $data = $this->parseMultiValueBlock($object, $reflection, $className);
        }

        return $data;
    }


    /**
      * Parse a response block annotated object with a single level of annotation...
      *
      * @param ResponseBlock $object        The response object to parse.
      * @param $reflection                  The reflection object for the responseblock.
      * @param String $classname            The name of the ResponseBlock object.
      *
      * @return DataNode
      */
    private function parseSingleValueBlock(
        ResponseBlock $object,
        $reflection,
        $className
    ) {
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $root = DataNode::makeSingleValueNode($className);

        foreach($properties as $property) {

            if ($property->hasAnnotation('RB_BlockContent')) {
               $root->setData($property->getValue($object));
            }
            if ($property->hasAnnotation('RB_BlockAttribute')) {
                $annotation = $property->getAnnotation('RB_BlockAttribute');
                $root->addAttribute($annotation->attribute, $property->getValue($object));
            }
        }

        foreach($methods as $method) {
            $methodName = $method->getName();

            if ($method->hasAnnotation('RB_BlockContent')) {
               $root->setData($object->$methodName());
            }
            if ($method->hasAnnotation('RB_BlockAttribute')) {
                $annotation = $method->getAnnotation('RB_BlockAttribute');
                $root->addAttribute($annotation->attribute, $object->$methodName());
            }
        }

        return $root;
    }

    /**
      * Parse a response block annotated object with multiple levels of annotation...
      *
      * @param ResponseBlock $object        The response object to parse.
      * @param $reflection                  The reflection object for the responseblock.
      * @param String $classname            The name of the ResponseBlock object.
      *
      * @return DataNode
      */
    private function parseMultiValueBlock(
        ResponseBlock $object,
        $reflection,
        $className
    ) {

        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $root = DataNode::makeNode($className);

        foreach($properties as $property) {
            $workingNode = $root;

            if ($property->hasAnnotation('RB_BlockParent')) {
                $parentBlock = $property->getAnnotation('RB_BlockParent')->value;

                // Get the DataNode for the parent... or make one if it doesn't exist.
                // We have an imposed depth limit of 2...
                $parentNode = $root->getDataNode($parentBlock);
                if ($parentNode == null) {
                    $parentNode = DataNode::makeNode($parentBlock);
                    $root->addData($parentNode);
                }

                $workingNode = $parentNode;
            }

            if ($property->hasAnnotation('RB_BlockProperty')) {
                $annotation = $property->getAnnotation('RB_BlockProperty');

                $objData = $property->getValue($object);

                if (is_object($objData) && $objData instanceof ResponseBlock) {
                    $node = $this->parseBlock($objData);
                    $workingNode->addData($node);
                } else if (is_array($objData)) {
                    $nonWereObjects = true;

                    foreach($objData as $objDatum) {
                        if (is_object($objDatum)) {
                            $nonWereObjects = false;
                            if ($objDatum instanceof ResponseBlock) {
                                $node = $this->parseBlock($objDatum);
                                $workingNode->addData($node);
                            }
                        }
                    }

                    if ($nonWereObjects) {
                        $node = DataNode::makeSingleValueNode($annotation->value, $property->getValue($object));
                        if ($property->hasAnnotation('RB_BlockArrayKey')) {
                            $node->setDataKey($property->getAnnotation('RB_BlockArrayKey')->value);
                        }
                        $workingNode->addData($node);
                    }
                } else {
                    $workingNode->addData(DataNode::makeNode($annotation->value, $property->getValue($object)));
                }
            }

            if ($property->hasAnnotation('RB_BlockAttribute')) {
                $annotation = $property->getAnnotation('RB_BlockAttribute');

                if ($annotation->property == 'root') {
                    $root->addAttribute($annotation->attribute, $property->getValue());
                } else {
                    $node = $workingNode->getData($annotation->property);
                    $node->addAttribute($annotation->attribute, $property->getValue());
                }
            }
        }

        // get all properties and attributes
        foreach($methods as $method) {
            $methodName = $method->getName();

            $workingNode = $root;

            if ($method->hasAnnotation('RB_BlockParent')) {
                $parentBlock = $method->getAnnotation('RB_BlockParent')->value;

                // Get the DataNode for the parent... or make one if it doesn't exist.
                // We have an imposed depth limit of 2...
                $parentNode = $root->getDataNode($parentBlock);
                if ($parentNode == null) {
                    $parentNode = DataNode::makeNode($parentBlock);
                    $root->addData($parentNode);
                }

                $workingNode = $parentNode;
            }

           if ($method->hasAnnotation('RB_BlockProperty')) {
                $annotation = $method->getAnnotation('RB_BlockProperty');

                $objData = $object->$methodName();

                if (is_object($objData)) {
                    if ($objData instanceof ResponseBlock) {
                        $node = $this->parseBlock($objData);
                        $workingNode->addData($node);
                    }
                } else if (is_array($objData)) {
                    $nonWereObjects = true;

                    foreach($objData as $objDatum) {
                        if (is_object($objDatum)) {
                            $nonWereObjects = false;
                            if ($objDatum instanceof ResponseBlock) {
                                $node = $this->parseBlock($objDatum);
                                $workingNode->addData($node);
                            }
                        }
                    }

                    if ($nonWereObjects) {
                        $node = DataNode::makeNode($annotation->value, $object->$methodName());
                        if ($method->hasAnnotation('RB_BlockArrayKey')) {
                            $node->setDataKey($method->getAnnotation('RB_BlockArrayKey')->value);
                        }
                        $workingNode->addData($node);
                    }
                } else {
                    $workingNode->addData(DataNode::makeSingleValueNode($annotation->value, $object->$methodName()));
                }
            }

            if ($method->hasAnnotation('RB_BlockAttribute')) {
                $annotation = $method->getAnnotation('RB_BlockAttribute');

                if ($annotation->property == 'root') {
                    $root->addAttribute($annotation->attribute, $object->$methodName());
                } else {
                    $node = $workingNode->getDataNode($annotation->property);
                    $node->addAttribute($annotation->attribute, $object->$methodName());
                }
            }
        }

        return $root;
    }
}
?>
