# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Changed
- Update Traefik version from 1.5.4 to 1.6.5
- Make 'help' the default command instead of 'wizard'
### Added
- Add help usages to Console API documentation.

## [0.3.1] - 2018-05-22
### Fixed
- Add --skip-dns-validation option to wizard command.

## [0.3.0] - 2018-05-21
### Added
- Examples to run commands interactively.
- `--skip-dns-validation` option.
### Changed
- Better and nicer complete message for wizard command.
- Validate write permissions for Digital Ocean token.

## [0.2.0] - 2018-05-13
### Added
- Suggest a static site command to run with valid values on install command complete.

## 0.1.0 - 2018-05-06
### Added
- Initial release.
- Command line interface for installing, adding a static site, and uninstalling.

[Unreleased]: https://github.com/chrif/cocotte/compare/0.3.1...HEAD
[0.3.1]: https://github.com/chrif/cocotte/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/chrif/cocotte/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/chrif/cocotte/compare/0.1.0...0.2.0
