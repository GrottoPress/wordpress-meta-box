# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2023-05-31

### Changed
- Upgrade `grottopress/wordpress-field` package to v1.0

## [0.4.8] - 2023-05-05

### Fixed
- Fix fatal error when saving metabox with a supplied meta key

## [0.4.7] - 2023-04-13

### Added
- Add support for PHP 8

### Changed
- Replace Travis CI with GitHub actions

## [0.4.6] - 2020-02-08

### Added
- Add support for PHP 7.4

### Fixed
- Fix deprecation notice in codeception

## 0.4.5 - 2019-04-18

### Fixed
- Fix field not saving if its ID is different from its name

## 0.4.4 - 2019-04-17

### Added
- Add `.gitattributes`

## 0.4.3 - 2019-04-16

### Fixed
- Fix composer dependency resolution failures in travis-ci

## 0.4.2 - 2019-04-16

### Added
- Add PHP 7.3 to travis-ci build matrix

## 0.4.1 - 2018-10-06

### Changed
- Rename `LICENSE.md` to `LICENSE`
- Move `lib/` to a new `src/` directory

## 0.4.0 - 2018-09-12

### Added
- Add `.editorconfig`

### Changed
- Rename `src/` directory to `lib/`

## 0.3.1 - 2018-08-21

### Fixed
- Update documentation to reflect previous release

## 0.3.0 - 2018-08-21

### Changed
- Move classes one level up the file system for a shorter namespace

## 0.2.1 - 2018-07-05

### Fixed
- Fix Error: "Illegal string offset 'fields'"

## 0.2.0 - 2018-07-05

### Changed
- Add fields to meta box callback args

## 0.1.1 - 2018-06-29

### Changed
- Ensure field value is string when post meta empty
- Use more specific type annotations for arrays in doc blocks

## 0.1.0 - 2018-03-06

### Added
- Initial public release
