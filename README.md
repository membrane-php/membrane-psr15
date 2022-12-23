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

### Usage

#### Request Validation

The `RequestValidation` middleware will validate or invalidate incoming requests and let you decide how to react.
You can precede it with your own custom middleware or precede it with one of the following built-in options:

#### Nested Json Response

The `ResponseJsonNested` MUST precede the `RequestValidation` middleware
as it relies on the container containing the result.
It will check whether the request has passed or failed validation.
Invalid requests will return a response detailing the reasons the request was invalid.

#### Flat Json Response

The `ResponseJsonFlat` MUST precede the `RequestValidation` middleware
as it relies on the container containing the result.
It will check whether the request has passed or failed validation.
Invalid requests will return a response detailing the reasons the request was invalid.
