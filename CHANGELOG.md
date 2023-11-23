# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [v3.3.0]
- Test against PHP 8.3
- Add Symfony 7 support
- Micro optimization to reduce array_merge calls

## [v3.2.0]
- Test against PHP 8.2
- Fix for missing tput binary by

## [v3.1.0]
- Allow `phpunit/php-timer`

## [v3.0.1]
- Fix version number

## [v3.0.0]
- Support symfony 6
- PHP 8.1 support
- Remove --non-zero-exit-on-violation option
- Drop php <7.4 support, symfony <4.4, add php 8.1 to pipeline

## [v2.5.0] - 2021-12-13
- Update php-parallel-lint/php-console-highlighter to get rid of abandoned dependency
- Run PHPMND against codebase
- Improve GitHub Actions pipelines

## [v2.4.0] - 2021-02-27
- Add GA instead of TravisCI
- Support PHP 8

## [v2.3.0] - 2020-04-15
- Support PHP 7.3 and PHP 7.4
- Update dependencies

## [v2.2.0] - 2019-01-12
- Add default ignore functions (intval, strval and floatval)
- Fix negative number whitelisting
- Ignore the negative value if the scalar does not have a value field
- Allow multiple files and directories

## [v2.1.0] - 2019-01-27
- Check magic numbers in constant arrays.
- Catch array[magic_number]
- Whitelist option. Link to a file containing filenames to search

## [v2.0.0] - 2018-03-17
- Update dependencies. Required PHP 7.1
- Add support for negative numbers.
- Ignore '0' and '1' by default.
- Add XML report output
- Option for allowing array mapping when using array extension
- Option for including numeric strings

## [v1.1.1] - 2017-05-16
- Fix `--non-zero-exit-on-violation` option.

## [v1.1.0] - 2017-05-15
- Add `--non-zero-exit-on-violation` option to return non zero exit code when there are magic number in the codebase.
- Add `--hint` option suggest replacements for magic numbers.
- Add more flexibility to extensions. 'all' option and possibility for removal with minus sign.
- Add `--suffixes` option.
- Add PHAR build support with Box.

## [v1.0.3] - 2017-04-27
### Added
- Add `--strings` option to include strings literals in code analysis.
- Add `--ignore-strings` option to ignore strings when using the `strings` option.

## [v1.0.2] - 2017-04-25
### Added
- Add `--exclude-path` option.
- Add `--exclude-file` option.
- Add `--ignore-funcs` option.
- Add total magic number count in output result.

## [v1.0.1] - 2017-04-21
### Fixed
- Ignore magic numbers in constants when there is operation.

## v1.0.0 - 2017-04-20
- Initial release.

[v1.1.1]: https://github.com/povils/phpmnd/compare/v1.1.0...v1.1.1
[v1.1.0]: https://github.com/povils/phpmnd/compare/v1.0.3...v1.1.0
[v1.0.3]: https://github.com/povils/phpmnd/compare/v1.0.2...v1.0.3
[v1.0.2]: https://github.com/povils/phpmnd/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/povils/phpmnd/compare/v1.0.0...v1.0.1
