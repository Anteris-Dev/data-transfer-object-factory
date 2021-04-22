# Changelog

## [1.0.0] - 2021-01-22

## Added
- Support for smart generation of properties. Faker will guess the type of data to be populated based on the property name (e.g. homeAddress property is populated with an address).
- Support for states.
- Support for sequences.
- Support for PHP 8.
- Support for the latest version of Spatie DTOs.

## Changed
- The factory API to be more fluent.
- The entry point to the `Anteris\DataTransferObjectFactory\Factory` class.
- Extensions to the property type generator are made through `PropertyFactory::registerProvider()`

## Removed
- Support for PHP 7.4.
- Support for collections.

## [0.1.2] - 2020-12-04
### Fixed
- Factory was not generating child DTOs for properties that were typed to a DTO.

## [0.1.1] - 2020-11-16
### Added
- Stricter type checks for phpDocumentor
- Style checks with PHP CS

### Changed
- Faker requirements to fakerphp/faker
- Composer requirements to be more compliant with semantic versioning
- Location of this CHANGELOG

## [0.1.0] - 2020-09-04
### Added
- Initial generator class.
- Support for doc block type casting.

[1.0.0]: https://github.com/anteris-dev/data-transfer-object-factory/compare/v0.1.2...v1.0.0
[0.1.2]: https://github.com/anteris-dev/data-transfer-object-factory/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/anteris-dev/data-transfer-object-factory/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/anteris-dev/data-transfer-object-factory/releases/tag/v0.1.0
