<p align="center">
<a href="https://travis-ci.com/develme/restful-lists"><img src="https://travis-ci.com/develme/restful-lists.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/develme/restful-lists"><img src="https://img.shields.io/packagist/dt/develme/restful-lists" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/develme/restful-lists"><img src="https://img.shields.io/packagist/v/develme/restful-lists" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/develme/restful-lists"><img src="https://img.shields.io/packagist/l/develme/restful-lists" alt="License"></a>
</p>


Restful Lists is a library that allows a list style endpoint to instantly support common features like filtering,
sorting, and pagination. Wrap your data in an engine or service class to quickly parse get/post variables detailing what
filters, orders, and pagination to apply.

## Install

```shell
composer require develme/restful-lists
```

## Data Types

Data types are the different forms of persistent data the engine attempts to filter, order and paginate. This is the
layer one can use to add support for various data layers like Redis, SQL, Plain Arrays, even data stored in files or
other API endpoints.

### Supported

- Collection
- Eloquent's Model Builder

### Coming Soon

- Array
- Eloquent's Query Builder

## Examples

### Using the service within a laravel/lumen controller

This example uses the service class. The service class automatically parses get and post vars to apply filtering, 
sorting and pagination. You can find example javascript and url structures below.

```php
use DevelMe\RestfulList\Model\Service;
use Symfony\Component\HttpFoundation\Response;
use Tests\Models\Example;

public function index(Example $example, Service $service): Response
{
    return $service->model($example)->json();
}
```

### Using the engine directly

```php

$myData = []; //

$data = new \Illuminate\Support\Collection($myData);

$orchestrator = new \DevelMe\RestfulList\Collection\Orchestration\Orchestrator();
$orchestrator->register();

$engine = new \DevelMe\RestfulList\Engines\Collection($data, $orchestrator);

// Filters
$engine->filters([
    'status' => 'Open', // status equals 'Open' - Simple filters default to equals
    'name' => ['field' => 'name', 'type' => 'contains', 'value' => 'John'], // name contains 'John' | Results in '%John%' for SQL
]);

// Orders
$engine->orders([
    'state', // order by state ascending - Simple orders default to ascending
    'created_at' => 'desc', // order by created_at descending - key => value equates to field => direction
    'title' => ['field' => 'title', 'direction' => 'desc'], // order by title descending - Explicitly defining field and direction
]);

// Pagination - simple
$engine->pagination([100, 200]); // start at 100 end at 200 - Index 0 is start, index 1 is end | offset 100 limit 100 for SQL

// Pagination - explicit
$engine->pagination(['start' => 100, 'end' => 200]);

// Results
$results = $engine->go();
```
Example javascript object structure
```javascript
let parameters = {
    "filters" : {
        'status' : 'Open', // status equals 'Open' - Simple filters default to equals
        'name' : {'field' : 'name', 'type' : 'contains', 'value' : 'John'}, // name contains 'John' | Results in '%John%' for SQL
    },
    'orders' : {
        'state' : null, // order by state ascending - Simple orders default to ascending
        'created_at' : 'desc', // order by created_at descending - key => value equates to field => direction
        'title' : {'field' : 'title', 'direction' : 'desc'}, // order by title descending - Explicitly defining field and direction
    },
    'pagination' : {
        'page' : 2,
        'size' : 30,
    }
}

// One could use jQuery.param to convert it to a query string or choose to post JSON back.
let query = jQuery.param(parameters);
let url = `https://www.develme.com/example?${query}`;
```

Example URL created from the javascript object
```
https://www.develme.com/example?filters%5Bstatus%5D=Open&filters%5Bname%5D%5Bfield%5D=name&filters%5Bname%5D%5Btype%5D=contains&filters%5Bname%5D%5Bvalue%5D=John&orders%5B0%5D=state&orders%5Bcreated_at%5D=desc&orders%5Btitle%5D%5Bfield%5D=title&orders%5Btitle%5D%5Bdirection%5D=desc&pagination%5Bpage%5D=2&pagination%5Bsize%5D=30
```
Example PHP array structure as a result of the javascript passed back
```php
$parameters = [
    'filters' => [
        'status' => 'Open', // status equals 'Open' - Simple filters default to equals
        'name' => ['field' => 'name', 'type' => 'contains', 'value' => 'John'], // name contains 'John' | Results in '%John%' for SQL
    ],
    'orders' => [
        'state', // order by state ascending - Simple orders default to ascending
        'created_at' => 'desc', // order by created_at descending - key => value equates to field => direction
        'title' => ['field' => 'title', 'direction' => 'desc'], // order by title descending - Explicitly defining field and direction
    ],
    'pagination' => [
        'page' => 2,
        'size' => 30,
    ]
]
```

## Customization

### Data Types

### Custom Data Types

Since data types are completely decoupled from the engine, it is fairly straight forward to add support for custom data
types. Implement the DevelMe\RestfulList\Contracts\Orchestration interface, taking care of the various calls the engine
makes to the implementation with various filter/sort/paginate settings. A custom engine is also needed to handle your
custom data type.

#### Instantiating the engine with a custom data type

```php
/**
 * Engine that supports the custom data
 */
class CustomDataEngine extends \DevelMe\RestfulList\Engines\Base 
{
    protected $data;

    public function __construct($customData, \DevelMe\RestfulList\Contracts\Orchestration $orchestrator) {
        parent::__construct($orchestrator);
        $this->data = $customData;
    }
}


/**
 * Handles calls for filtering, sorting, paginating and fetching results. Naturally the Orchestrator wants to use the 
 * Strategy behavioral pattern to break out the necessary functionality into smaller class implementations.
 * 
 * Below, we are returning CustomDataHandler which can implement the interfaces specified in the method tags, keeping
 * all the code together, if desired
 * 
 * @method \DevelMe\RestfulList\Contracts\Counter counter()
 * @method \DevelMe\RestfulList\Contracts\Engine\Filtration filter()
 * @method \DevelMe\RestfulList\Contracts\Engine\Arrangement order()
 * @method \DevelMe\RestfulList\Contracts\Engine\Paginator pagination()
 * @method \DevelMe\RestfulList\Contracts\Engine\Result result()
 */
class CustomOrchestrator implements \DevelMe\RestfulList\Contracts\Orchestration
{
    /**
     * We're letting CustomDataHandler handle everything, but one should leverage the dynamic method call implemented
     * here to return single responsibility implementations of what is being requested in the $ask variable.
     */
    public function orchestrate(string $ask):  mixed
    {
        return new CustomDataHandler;
    }
    
    public function __call(string $name,array $arguments)
    {
        $this->orchestrate($name);
    }
}


/** 
 * This handler can handle everything. It has way too many responsibilities, and breaking the behaviors into different
 * class implementations would be the desired result.
 */
class CustomDataHandler implements 
    \DevelMe\RestfulList\Contracts\Counter,
    \DevelMe\RestfulList\Contracts\Engine\Filtration,
    \DevelMe\RestfulList\Contracts\Engine\Arrangement,
    \DevelMe\RestfulList\Contracts\Engine\Paginator, 
    \DevelMe\RestfulList\Contracts\Engine\Result
{
    public function arrange(\DevelMe\RestfulList\Contracts\Engine\Data $data,array $orders)
    {
        // TODO: Implement arrange() method.
    }
    
    public function count(\DevelMe\RestfulList\Contracts\Engine\Data $data) : int
    {
        // TODO: Implement count() method.
    }
    
    public function filter(\DevelMe\RestfulList\Contracts\Engine\Data $data,array $filters)
    {
        // TODO: Implement filter() method.
    }
    
    public function paginate(\DevelMe\RestfulList\Contracts\Engine\Data $data,array $pagination)
    {
        // TODO: Implement paginate() method.
    }
    
    public function get(\DevelMe\RestfulList\Contracts\Engine\Data $data)
    {
     // TODO: Implement get() method.
    }
}

$data = new My\Custom\XML\Loader('path-to-xml-file.xml');

$orchestrator = new CustomOrchestrator();

$engine = new CustomDataEngine($data, $orchestrator);

$results = $engine->filters([])->orders([])->pagination([])->go();
```
