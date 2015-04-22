# Creating the Controller #

A Controller must inherit from the Controller class :

```
use restbed\Controller;

class SampleController extends Controller {

.
.
.

}
```


# Define the REST methods #

To define the methods that should be exposed, we need to annotate them with : [RB\_Control](Annotations#RB_Control.md)

```
/**
 * @RB_Control(rmethod="GET", pattern="$id")
 *
 * Handle request for a GET by sample id.
 */
public function getSample(
    $id
) {
    return Sample::loadByUid($id);
}
```

# 'Registering' the controller in restbed #

With the controller finished, we then add it to the resources config file.

default location is _restbed/config/resources.conf.php_ , but can be changed by editing the Config::RESOURCE\_FILE constant.

All we need to do, is include the Controller and define its end point :

```
require_once($resourceBaseDir.'sample/SampleController.class.php');
$_RESOURCE['sample']['controller'] = 'SampleController';
```

Now the getSample() function can be accessed with
`http://restbed_host/sample/$id`  (Where $id is the sample id, ie 1, 2 etc)