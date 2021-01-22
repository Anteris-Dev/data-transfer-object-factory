# Making it easy to mock your Data Transfer Objects
[![Tests](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3AStyle)

This package supports DTO mocking for the Spatie [Data Transfer Object package](https://github.com/spatie/data-transfer-object). By default only built in PHP types are supported, but this factory can easily be extended to support other types (e.g. Carbon) as well.

# To Install
Run `composer require anteris-dev/data-transfer-object-factory`.

# Getting Started

If you are simply using PHP default types in your DTOs, you can get started right away. Just pass your DTO FQDN to the static dto method. Calling this method on the factory returns an instance of `Anteris\DataTransferObjectFactory\DataTransferObjectFactory` which provides the following methods.

- `collection()` - _Allows you to specify a collection class to populate with the DTO._
- `count()` - _Allows you to specify how many DTOs to be generated. By default they will be returned in an array unless a collection is specified._
- `make()` - _Called when you are ready to generate the DTO. Returns the generated DTO._
- `random()` - _Generates a random number of DTOs_
- `sequence()` - _Alternates a specific state. (See below)_
- `state()` - _Manually sets properties based on the array of values passed._

Examples of these methods can be found below.

```php

use Anteris\DataTransferObjectFactory\Factory;

// Creates one DTO
Factory::dto(PersonData::class)->make();

// Creates two DTOs in an array
Factory::dto(PersonData::class)->count(2)->make();

// Creates a random number of DTOs in a collection
Factory::dto(PersonData::class)
    ->random()
    ->collection(PersonCollection::class)
    ->make();

// Sets the first name of every person to "Jim"
Factory::dto(PersonData::class)
    ->random()
    ->collection(PersonCollection::class)
    ->state([
        'firstName' => 'Jim',
    ])
    ->make();

// Also sets the first name of every person to "Jim"
Factory::dto(PersonData::class)
    ->random()
    ->collection(PersonCollection::class)
    ->make([
        'firstName' => 'Jim',
    ]);

// Alternates the names of each person between "Jim" and "Susie"
Factory::dto(PersonData::class)
    ->random()
    ->collection(PersonCollection::class)
    ->sequence(
        [ 'firstName' => 'Jim' ],
        [ 'firstName' => 'Susie' ]
    )
    ->make();

```

While you can generate a collection from the DTO factory, you can also do so from the collection factory. This is returned when you call the `collection()` method on the factory class and exposes the following methods.

- `fill()` - _Allows you to pass in an array of DTOs to fill the collection with. Should not be used with `of()`._
- `make()` - _Makes the collection._
- `of()` - _Allows you to pass a DTO class that the collection will be filled with. Should not be used with `fill()`._
- `sequence()` - _Alternates a specific state on the DTOs it contains. (See below)_
- `state()` - _Manually sets properties on the DTOs based on the array of values passed._

Examples of these methods can be found below.

```php

use Anteris\DataTransferObjectFactory\Factory;

// This will create a new collection of people with fake data
Factory::collection(PersonCollection::class)
    ->of(PersonData::class)
    ->make();

// This will create a new collection and fill it with the DTOs we pass
$dtos = Factory::dto(PersonData::class)->count(15)->make();
Factory::collection(PersonCollection::class)->fill($dtos)->make();

// Sets the first name of every person to "Jim"
Factory::collection(PersonCollection::class)
    ->dto(PersonData::class)
    ->state([
        'firstName' => 'Jim',
    ])
    ->make();

// Also sets the first name of every person to "Jim"
Factory::collection(PersonCollection::class)
    ->dto(PersonData::class)
    ->make([
        'firstName' => 'Jim',
    ]);

// Alternates the names of each person between "Jim" and "Susie"
Factory::collection(PersonCollection::class)
    ->dto(PersonData::class)
    ->sequence(
        [ 'firstName' => 'Jim' ],
        [ 'firstName' => 'Susie' ]
    )
    ->make();

```

## Extending

It used to be that you had to extend the factory class to utilize custom types. You can now do so through the static `registerProvider()` method on the `PropertyFactory` class. This method takes two arguments. The first should be the FQDN of the class you are providing (e.g. `Carbon\Carbon`) OR the built-in type (e.g. `string`). The second should be a callback that returns the generated value. This callback is passed two properties when called to assist in generating the value. The first is an instance of `Anteris\FakerMap\FakerMap` which can be used to help generate fake data. The second is an instance of `ReflectionProperty` which contains information about the property being generated.

For example, to support Carbon:

```php

use Anteris\DataTransferObjectFactory\PropertyFactory;

use Anteris\FakerMap\FakerMap;

PropertyFactory::registerProvider('Carbon\Carbon', fn(FakerMap $fakerMap) => Carbon::parse(
    $fakerMap->closest('dateTime')->fake()
));

```
