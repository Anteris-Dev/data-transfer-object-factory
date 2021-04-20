<?php

namespace Anteris\DataTransferObjectFactory;

use Anteris\FakerMap\FakerMap;

class PropertyFactory
{
    private static $providers = [];
    
    private FakerMap $fakerMap;

    private ?string $name;

    private array $types = [];

    public function __construct(?FakerMap $fakerMap = null)
    {
        $this->fakerMap = $fakerMap ?? FakerMap::new();
    }

    public static function new(): static
    {
        return new static;
    }

    public static function registerProvider(string $type, callable $callback): void
    {
        static::$providers[$type] = $callback;
    }

    public function name(string $name): static
    {
        $clone       = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function type(string $type): static
    {
        $clone          = clone $this;
        $clone->types[] = $type;

        return $clone;
    }

    public function types(array $types): static
    {
        $clone        = clone $this;
        $clone->types = $types;

        return $clone;
    }

    public function make()
    {
        if (isset($this->name)) {
            $guessedValue = $this->fakerMap->new()->closest($this->name)->fake();

            if (
                $guessedValue != null &&
                (
                    count($this->types) <= 0 ||
                    in_array(gettype($guessedValue), $this->types)
                )
            ) {
                return $guessedValue;
            }
        }

        return $this->makeProperty();
    }

    private function makeProperty()
    {
        // Get a type to generate.
        if (empty($this->types)) {
            $type = $this->getRandomType();
        } else {
            $type = $this->types[array_rand($this->types, 1)];
        }

        // Check providers first.
        if (isset(static::$providers[$type])) {
            return static::$providers[$type](FakerMap::new(), $this->name ?? null);
        }

        // Generate one of our PHP default types.
        switch ($type) {
            case 'array':
                return FakerMap::faker()->words();
            break;

            case 'bool':
                return FakerMap::faker()->boolean();
            break;

            case 'DateTime':
                return FakerMap::faker()->dateTime();
            break;

            case 'int':
                return FakerMap::faker()->randomDigit();
            break;

            case 'float':
                return FakerMap::faker()->randomFloat();
            break;

            case 'null':
                return null;
            break;
        }

        return FakerMap::faker()->word();
    }

    public function getRandomType(): string
    {
        $types = array_merge(array_keys(static::$providers), [
            'array',
            'bool',
            'DateTime',
            'int',
            'float',
            'string',
            'null',
        ]);

        return $types[array_rand($types, 1)];
    }
}
