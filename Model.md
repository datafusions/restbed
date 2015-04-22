# Introduction #

Models are decorated into Views by way of Annotations.


# Creating a Model #

Create your Model object as you would anywhere. We will use the Sample Model found in the sample code as example.

Here is our base Model :

```
class Sample {

    private $name;
    private $number;

    public function __construct(
        $name,
        $number
    ) {
        $this->name = $name;
        $this->number = $number;
    }

    public function getName() { return $this->name; }

    public function getNumber() {return $this->number; }
}
```

To have the object decorated by the framework, we need to have it extend the ResourceBase abstract class :

```
class Sample extends restbed\resource\ResourceBase {
```

Which involves passing some data to its overloaded constructor, a Unique ID, and optionally a Last Modified Timestamp :

NOTE: in this example we use the $name variable as the UID.
```
public function __construct(
        $name,
        $number
    ) {
        parent::__construct($name, null);
        $this->name = $name;
        $this->number = $number;
    }
```

# Annotate #

Now we can Annotate the class with [RB\_MultiValueBlock](Annotations#RB_MultiValueBlock.md) to indicate that we have nested values in this Node :
```
/**
 * @RB_MultiValueBlock
 */
class Sample extends restbed\resource\ResourceBase {
```

Then the methods are Annotated with [RB\_BlockProperty](Annotations#RB_BlockProperty.md) :

```
    /** @RB_BlockProperty("name") */
    public function getName() { return $this->name; }

    /** @RB_BlockProperty("number") */
    public function getNumber() {return $this->number; }
```

# Sample Class #

```
/**
 * @RB_MultiValueBlock
 */
class Sample extends restbed\resource\ResourceBase {

    private $name;
    private $number;

    public function __construct(
        $name,
        $number
    ) {
        parent::__construct($name, null);
        $this->name = $name;
        $this->number = $number;
    }

    /** @RB_BlockProperty("name") */
    public function getName() { return $this->name; }

    /** @RB_BlockProperty("number") */
    public function getNumber() {return $this->number; }
}
```

# View #

This will Render the Model into this XML View :

```
  <sample>
    <name>LUAE</name>
    <number>42</number>
  </sample>
```