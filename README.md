# Membrane-Psr15

Integrates [Membrane-core](https://github.com/membrane-php/membrane-core) with any project implementing Psr7.

## About

Middleware that validates the raw user input from incoming HTTP requests against your OpenAPI spec.  
Adds a `Membrane\Result\Result` onto your `ContainerInterface`.  
The Result object contains the cleaned up data and additional details in the case of invalid requests.

## Setup

### Installation

Require the `membrane/psr15` package in your composer.json and update your dependencies:

```text
composer require membrane/laravel
```

## Usage

### Requests

The `\Membrane\Psr15\Middleware\RequestValidation` middleware will validate or invalidate incoming requests and let you decide how to react.
You can precede it with your own custom middleware or precede it with one of the following built-in options:

### Responses

Any response middleware MUST follow the `RequestValidation` middleware as it requires the `result` object being added to
your container.  
These middlewares will check whether the request has passed or failed validation.  
Invalid requests will return an appropriate response detailing the reasons the request was invalid.

#### Flat Json

`\Membrane\Psr15\Middleware\ResponseJsonFlat`

**Example Output**

```text
{
    "errors":{
        "pet->id":["must be an integer"],
        "pet":["name is a required field"]
    },
    "title":"Request payload failed validation",
    "type":"about:blank",
    "status":400
}
```

#### Nested Json

`\Membrane\Psr15\Middleware\ResponseJsonNested`

**Example Output**

```text
{
    "errors":{
        "errors":[],
        "fields":{
            "pet":{
                "errors":[
                    "name is a required field"
                ],
                "fields":{
                    "id":{
                        "errors":[
                            "must be an integer"
                        ],
                        "fields":[]
                    }
                }
            }
        }
    },
    "title":"Request payload failed validation",
    "type":"about:blank",
    "status":400
}
```
