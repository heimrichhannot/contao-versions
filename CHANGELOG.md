# Changelog
All notable changes to this project will be documented in this file.

## [2.4.2] - 2025-02-27
- Changed: require at least php 7.
- Changed: code quality improvements
- Fixed: array key warning

## [2.4.1] - 2023-10-24
- Fixed: deprecated dbal method

## [2.4.0] - 2021-08-31
- Added: support for php 8

## [2.3.1] - 2021-08-04
- Fixed: exception when Versions class has not user set in frontend

## [2.3.0] - 2021-07-15
- Added: configuration for persistent tables and persistent tables version period
- Added: utils bundle dependency
- Changed: refactored Versions class to contao 4.9 base
- Changed: refactored legacy classes into services
- Changed: removed Haste_plus dependencies
- Deprecated: Version, VersionUser and VersionModel
- Fixed: Ambiguous class resolution warning

## [2.2.0] - 2020-06-29
- removed `Encryption` call -> not usable in php 7.4 anymore

## [2.1.0] - 2017-10-22

### Changes
- removed Symfony dependency
- updated php dependency
- updated licence

## [2.0.0] - 2017-06-20

### Changed
- first version for contao 4

## [1.0.14] - 2017-06-08

### Removed
- composer-installer dep

## [1.0.13] - 2017-04-12

### Added
- new tag

## [1.0.12] - 2017-04-06

### Changed
- added php7 support. fixed contao-core dependency

## [1.0.11] - 2017-02-16

### Fixed
- replace invalid method call clearVersionTable -> cleanVersionTable

## [1.0.10] - 2017-01-18

### Added
- findCurrent() und findPrevious() shorthands if no model is at hand
- replaced array() by []

## [1.0.9] - 2017-01-09

### Fixed
- missing user issues

## [1.0.8] - 2016-12-16

### Fixed
- merged changes from \Contao\Versions 3.5.19 into overwritten \Contao\Versions

## [1.0.7] - 2016-12-15

### Fixed
- do no longer truncate tables not within `$GLOBALS['PERSISTENT_VERSION_TABLES']`
