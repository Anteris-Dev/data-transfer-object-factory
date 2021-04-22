# Making it easy to mock your Data Transfer Objects
[![Tests](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3AStyle)

This package provides a fluent factory for Data Transfer Objects. Natively supported are POPOs (Plain Old PHP Objects), Getter / Setter Objects, and [Spatie's Data Transfer Objects](https://github.com/spatie/data-transfer-object). If there is a DTO not represented, you can easily add an adapter to support it.

One thing that makes this package so powerful is that it integrates with Faker in an attempt to intelligently generate the correct content for your data based on its name. For example, a DTO with the property "$firstName" will get a Faker first name.

By default only built-in PHP types are supported, but this factory can easily be extended to support other types (e.g., Carbon) as well.

# To Install
Run `composer require anteris-dev/data-transfer-object-factory`.

# Getting Started
If you are simply using PHP default types in your DTOs, you can get started right away. Just pass your DTO FQDN to the static `new()` method. You can then use any of the following helper methods.

- `count()` - _Allows you to specify how many DTOs to be generated. They will be returned in an array._
- `make()` - _Called when you are ready to generate the DTO. Returns the generated DTO[s]._
- `random()` - _Generates a random number of DTOs_
- `sequence()` - _Alternates a specific state. (See below)_
- `state()` - _Manually sets properties based on the array of values passed._

Examples of these methods can be found below.

```php

use Anteris\DataTransferObjectFactory\Factory;

// Creates one DTO
Factory::new(PersonData::class)->make();

// Creates two DTOs in an array
Factory::new(PersonData::class)->count(2)->make();

// Sets the first name of every person to "Jim"
Factory::new(PersonData::class)
    ->random()
    ->state([
        'firstName' => 'Jim',
    ])
    ->make();

// Also sets the first name of every person to "Jim"
Factory::dto(PersonData::class)
    ->random()
    ->make([
        'firstName' => 'Jim',
    ]);

// Alternates the names of each person between "Jim" and "Susie"
Factory::dto(PersonData::class)
    ->random()
    ->sequence(
        [ 'firstName' => 'Jim' ],
        [ 'firstName' => 'Susie' ]
    )
    ->make();

```

## Extending

### Adapters
Adapters instruct the factory on how to retrieve properties for a specific type of class. Adapters must implement the `Anteris\DataTransferObjectFactory\Adapter\AdapterInterface` which requires the following methods.

- `handles(ReflectionClass $class)` - _Returns a bool if the adapter can handle the referenced reflection class._
- `getProperties(ReflectionClass $class)` - _Returns a collection of properties found on the referenced reflection class._
- `createClass(ReflectionClass $class, PropertyCollection $collection)` - _Creates and returns and instance of the reflection class using the properties passed._

To register an adapter on the factory, call its static `registerAdapter()` method. For example:

```php

use Anteris\DataTransferObjectFactory\Factory;

Factory::registerAdapter(new MyCustomAdapter);

```

For more information check out the `Adapter` directory in the source code.

### Property Types

It used to be that you had to extend the factory class to utilize custom types. You can now do so through the static `registerProvider()` method on the `PropertyFactory` class. This method takes two arguments. The first should be the FQDN of the class you are providing (e.g. `Carbon\Carbon`) OR the built-in type (e.g. `string`). The second should be a callback that returns the generated value. This callback is passed two properties when called to assist in generating the value. The first is an instance of `Anteris\FakerMap\FakerMap` which can be used to help generate fake data. The second is the name of the property being generated or null if not provided.

For example, to support Carbon:

```php

use Anteris\DataTransferObjectFactory\PropertyFactory;

use Anteris\FakerMap\FakerMap;

PropertyFactory::registerProvider('Carbon\Carbon', fn(FakerMap $fakerMap) => Carbon::parse(
    $fakerMap->closest('dateTime')->fake()
));

```
