# Making it easy to mock your Data Transfer Objects
[![Test](https://github.com/Anteris-Dev/data-transfer-object-factory/workflows/Test/badge.svg)](https://github.com/Anteris-Dev/data-transfer-object-factory/actions?query=workflow%3ATest)

This package supports DTO mocking for the Spatie [Data Transfer Object package](https://github.com/spatie/data-transfer-object). By default only built in PHP types are supported, but this factory can easily be extended to support other types (e.g. Carbon) as well.

- **Note**: Right now doc block type casting is not supported.

# To Install

Run `composer require anteris-dev/data-transfer-object-factory:dev-master`.

- **Note**: This package require PHP 7.4 so it can take full advantage of type casting in PHP.

# Getting Started

If you are simply using PHP default types in your DTOs, you can get started right away. Just pass your class name to the static make method.

For example:

```php

use Anteris\Example\DataTransferObject;
use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;

// This will create a new instance of my DTO with fake data
$object = DataTransferObjectFactory::make( DataTransferObject::class );

```

You can even fake a collection of DTOs.

```php

use Anteris\Example\DataTransferObject;
use Anteris\Example\DataTransferObjectCollection;
use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;

// This will create a new collection of my DTOs with fake data
$collection = DataTransferObjectFactory::makeCollection(
    DataTransferObject::class,
    DataTransferObjectCollection::class
);

```

You can easily extend the factory to support other data types. To do this, create a static method with the class name prefixed by "make" (e.g. 'makeCarbon'). This should return fake data. An instance of faker can always be retrieved by calling `static::faker()`.

For example, to support Carbon:

```php

use Anteris\DataTransferObjectFactory\DataTransferObjectFactory;
use Carbon\Carbon;

class Factory extends DataTransferObjectFactory
{
    public static function makeCarbon(): Carbon
    {
        return new Carbon(
            static::faker()->date
        );
    }
}

```
