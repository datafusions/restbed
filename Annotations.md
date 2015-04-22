Annotations library provided by [Addendum](http://code.google.com/p/addendum/)

All Annotations must be prefixed with '@'.

# Controller #

These are used inside the Controller for defining routes.


## RB\_Control ##

| **parameter** | **Description** | **Values** |
|:--------------|:----------------|:-----------|
| rmethod | The Request Method type | GET | POST | PUT | DELETE | OPTION |
| pattern | The url pattern to match | string |

**Note on pattern :**

can use php-like variable syntax to define wildcards, eg :

pattern="$id/status/$part"

Will match :
  * 2/status/wrench
  * someid/status/123
  * etc...

```
/**
 * @RB_Control(rmethod="GET", pattern="$id")
 */
```

# Decorator #

These are used to define how a Model is converted to a View.

A Node is an annotated code block. (a Class, a Method or a Member)

## RB\_SingleValueBlock ##

Indicates the the following Node contains a single value.

Usually used to Annotate the Model class.

## RB\_MultiValueBlock ##

Indicates the the following Node contains a multiple value and should be recursively parsed.

Usually used to Annotate the Model class.

## RB\_BlockProperty ##

| **parameter** | **Description** | **Values** |
|:--------------|:----------------|:-----------|
|  | The name of the property | String |

A BlockProperty can best be explained as an XML Element. It has a name (set by the parameter) and a value (The result of a Method or value of Member)

**Example**

```
/**                                                               
 * Basic Property Node.                                           
 *                                                                
 * @RB_BlockProperty("text")                                      
 */                                                               
public function getTextData() { return "stuff"; }             
```

Parse with XmlDecorator to :
```
<text>stuff</text>
```

## RB\_BlockAttribute ##

| **parameter** | **Description** | **Values** |
|:--------------|:----------------|:-----------|
| property | The name of the property to add the attribute to | String |
| attribute | The name of the attribute | String |

**Example**

```
/**                                                               
 * Adding Attribute to a Property Node.                           
 *                                                                
 * @RB_BlockAttribute(property="text", attribute="textAttribute") 
 */                                                               
public function getTextAttrib() { return "attrib"; }              
```

Parse with XmlDecorator to :
```
<text textAttribute="attrib">stuff</text>
```

## RB\_BlockArrayKey ##
| **parameter** | **Description** | **Values** |
|:--------------|:----------------|:-----------|
|  | The value of the array key | String |

Used with RB\_BlockProperty

**Example**

```
/**
 * @RB_BlockProperty("anArray")                        
 * @RB_BlockArrayKey("element")                        
 */                                                    
public function getElementArray() {                    
    return Array('fire', 'water', 'earth', 'air');     
}
```

Parse with XmlDecorator to :
```
<anArray>
    <element>fire</element>
    <element>water</element>
    <element>earth</element>
    <element>air</element>
</anArray>
```

## RB\_BlockParent ##

**Example**

```
/**                                                     
 * @RB_BlockProperty("TheChild")                        
 * @RB_BlockParent("TheParent")                         
 */                                                     
public function getChild() { return "I am the child"; } 
```

Parse with XmlDecorator to :
```
<TheParent>
    <TheChild>I am the child</TheChild>
</TheParent>
```

## RB\_BlockName ##

Used to override the default block name.

The default block name in a Model is the lower case version of it's Class name :
```
class Sample extends ResourceBase {
```

Would make a block named 'sample'.

This can be changed by adding this annotation :
```
/**
 * @RB_BlockName("examples")
 */
class TestModel extends restbed\resource\ResourceBase {
```

Would make a block named 'examples' instead of 'testmodel'.