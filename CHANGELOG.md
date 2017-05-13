# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
- Add `--non-zero-exit-on-violation` option to return non zero exit code when there are magic number in the codebase.

## [v1.0.3] - 2017-04-27
### Added
- Add `--strings` option to include strings literals in code analysis.
- Add `--ignore-strings` option to ignore strings when using the `strings` option.

## [v1.0.2] - 2017-04-25
### Added
- Add `--exclude-path` option.
- Add `--exclude-name` option.
- Add `--ignore-funcs` option.
- Add total magic number count in output result.

## [v1.0.1] - 2017-04-21
### Fixed
- Ignore magic numbers in constants when there is operation.

## v1.0.0 - 2017-04-20
- Initial release.

[Unreleased]: https://github.com/povils/phpmnd/compare/v1.0.3...HEAD
[v1.0.3]: https://github.com/povils/phpmnd/compare/v1.0.2...v1.0.3
[v1.0.2]: https://github.com/povils/phpmnd/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/povils/phpmnd/compare/v1.0.0...v1.0.1
