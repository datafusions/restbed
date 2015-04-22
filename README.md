#RESTbed

RESTbed is an experimental lightweight MVC framework to enable the construction of REST services.

The idea behind this framework is to leverage the use of Annotations in the Model classes to generate the Views. Annotations are already used in Controllers in other frameworks, so this experiment extends this idea to the Models as well.

A basic example :

This is a sample Model with 2 Member variables.

    /**@RB_MultiValueBlock*/
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
    
When decorated with the XMLDecorator will output :

    <sample>
        <name>LUAE</name>
        <number>42</number>
    </sample>
    
In turn this block is wrapped in an 'envelop' (name is configurable) by the controller class with an optional messages element. The controller's action is described as below :

    /**
    * @RB_Control(rmethod="GET", pattern="$id")
    *
    * Handle request for a GET by sample id.
    */
    public function getSample(
        $id
    ) {
        Response::getInstance()->addMessage('View of Sample Class', 'log');
        return new Sample($id, 123);
    }

Accessing http://domain.com/sample/test will return :

    <restbed>
        <messages>
            <message type="log">View of Sample Class</message>
        </messages>
        <sample>
            <name>test</name>
            <number>123</number>
        </sample>
    </restbed>
  
Please note that this framework is completely experimental and not fit for production environments.
