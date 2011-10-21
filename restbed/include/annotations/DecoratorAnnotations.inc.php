<?php
/**
 *
 */
class RB_SingleValueBlock extends Annotation {}

/**
 *
 */
class RB_MultiValueBlock extends Annotation {}

/**
 *
 */
class RB_BlockName extends Annotation {}

/**
 *
 */
class RB_BlockContent extends Annotation {}

/**
 *
 */
class RB_BlockProperty extends Annotation {}

/**
 *
 */
class RB_BlockAttribute extends Annotation{
    public $property;
    public $attribute;
}

/**
 *
 */
class RB_BlockParent extends Annotation {}

/**
 *
 */
class RB_BlockArrayKey extends Annotation {}
?>
