# Making it easy to mock your Data Transfer Objects
[![Tests](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3AStyle)

This package supports DTO mocking for the Spatie [Data Transfer Object package](https://github.com/spatie/data-transfer-object). By default only built in PHP types are supported, but this factory can easily be extended to support other types (e.g. Carbon) as well.

# To Install
Run `composer require anteris-dev/data-transfer-object-factory`.

- **Note**: This package require PHP 7.4 so it can take full advantage of type casting in PHP.

Next we recommend checking out the [documentation](https://anteris.dev/dto-factory)!

# Getting Started

If you are simply using PHP default types in your DTOs, you can get started right away. Just pass your DTO FQDN to the static dto method. Calling this method on the factory returns an instance of `Anteris\DataTransferObjectFactory\DataTransferObjectFactory` which provides the following methods.

- `collection()` - _Allows you to specify a collection class to populate with the DTO._
- `count()` - _Allows you to specify how many DTOs to be generated. By default they will be returned in an array unless a collection is specified._
- `make()` - _Called when you are ready to generate the DTO. Returns the generated DTO._
- `random()` - _Generates a random number of DTOs_

Examples of these methods can be found below.

```php

use Anteris\Example\DataTransferObject;
use Anteris\Example\DataTransferObjectCollection;
use Anteris\DataTransferObjectFactory\Factory;

// Creates one DTO
Factory::dto(DataTransferObject::class)->make();

// Creates two DTOs in an array
Factory::dto(DataTransferObject::class)->count(2)->make();

// Creates a random number of DTOs in a collection
Factory::dto(DataTransferObject::class)
    ->random()
    ->collection(DataTransferObjectCollection::class)
    ->make();

```

While you can generate a collection from the DTO factory, you can also do so from the collection factory. This is returned when you call the `collection()` method on the factory class and exposes the following methods.

- `fill()` - _Allows you to pass in an array of DTOs to fill the collection with. Should not be used with `of()`._
- `make()` - _Makes the collection._
- `of()` - _Allows you to pass a DTO class that the collection will be filled with. Should not be used with `fill()`._

Examples of these methods can be found below.

```php

use Anteris\Example\DataTransferObject;
use Anteris\Example\DataTransferObjectCollection;
use Anteris\DataTransferObjectFactory\Factory;

// This will create a new collection of my DTOs with fake data
Factory::collection(DataTransferObjectCollection::class)
    ->of(DataTransferObject::class)
    ->make();

// This will create a new collection and fill it with the DTOs we pass
$dtos = Factory::dto(DataTransferObject::class)->count(15)->make();
Factory::collection(DataTransferObjectCollection::class)->fill($dtos)->make();

```

It used to be that you had to extend the factory class to utilize custom types. You can now do so through the `registerProvider()` method on the `PropertyFactory` class. This method takes two arguments. The first should be the FQDN of the class you are providing (e.g. `Carbon\Carbon`) OR the built-in type (e.g. `string`). The second should be a callback that returns the generated value. This callback is passed an instance of `Anteris\FakerMap\FakerMap` when called and can be utilized to assist in generating the value.

For example, to support Carbon:

```php

use Anteris\DataTransferObjectFactory\PropertyFactory;

PropertyFactory::registerProvider('Carbon\Carbon', fn($fakerMap) => Carbon::parse(
    $fakerMap->closest('dateTime')->fake()
));

```
