<?php
/**
 * Model showing example of the Annotations.
 *
 *
 * @RB_MultiValueBlock
 * @RB_BlockName("examples")
 */
class TestModel extends restbed\resource\ResourceBase {

    /**
     * A public member can be a simple Property Node.
     *
     * @RB_BlockProperty("publicMember") 
     */
    public $publicMember = "publicMember";

    private $text;

    public function __construct(
        $uid,
        $text
    ) {
        parent::__construct($uid, null);
        $this->text = $text; 
    }

    /**
     * Basic Property Node.
     * 
     * @RB_BlockProperty("text")
     */
    public function getTextData() { return $this->text; }

    /**
     * Adding Attribute to a Property Node.
     * 
     * @RB_BlockAttribute(property="text", attribute="textAttribute")
     */
    public function getTextAttrib() { return "attrib"; }

    /**
     * Arrays, can be Property Nodes too, But the key is used as each element's key.
     * This is a Numeric indexed array.
     * 
     * @RB_BlockProperty("numericArray")
     */
    public function getNumArray() {
        return Array('a', 'b', 'c', 'd');
    }

    /**
     * Arrays, can be Property Nodes too, But the key is used as each element's key.
     * This is an Associative indexed array.
     * 
     * @RB_BlockProperty("associativeArray")
     */
    public function getAssocArray() {
        return Array('One'=>1, 'Two'=>2, 'Three'=>3, 'Four'=>4);
    }

    /**
     * Arrays, can be Property Nodes too, But the key is used as each element's key.
     * This is a Numeric indexed array but using a custom key name.
     * 
     * @RB_BlockProperty("anArray")
     * @RB_BlockArrayKey("element")
     */
    public function getElementArray() {
        return Array('fire', 'water', 'earth', 'air');
    }

    /**
     * If a method returns NULL, the property is omitted.
     * Well. It should but its broken.
     *
     * @RB_BlockProperty('YouCantSeeMe")
     */
    public function sayWhat() { return null; }

    /**
     * The element nested into the Parent.
     *
     * @RB_BlockProperty("TheChild")
     * @RB_BlockParent("TheParent")
     */
    public function getChild() { return "I am the child"; }

    /**
     * You can also nest into other Nodes.
     *
     * This is the Parent
     * @RB_BlockProperty("TheOtherChild")
     * @RB_BlockParent("TheParent")
     */
    public function getParent() { return "I am the other child"; }


}
?>
