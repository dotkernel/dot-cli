# Changelog

All notable changes to this project are documented in this file.

Dates before 4.0.0 are the commit dates of the tagged commits.

## 4.0.0 - Unreleased

### Changed

- Minimum PHP version is now 8.3. Supported versions: 8.3, 8.4, 8.5 and 8.6.
- A command's lock now uses its resolved name, so aliases and abbreviations share one lock. Unknown commands are not locked.
- Lock errors are written to the error output with quiet verbosity, so they are shown with `-q` (use `--silent` to hide them).
- A missing or unwritable lock directory, or a lock file that cannot be opened, throws `Dot\Cli\Exception\RuntimeException` instead of a `TypeError`.
- `FileLocker::getLockFilePath()` throws `Dot\Cli\Exception\RuntimeException` when the directory or the command name is missing.
- `FileLocker::unlock()` releases a held lock even after `disable()`, and does nothing when no lock is held.
- The custom event dispatcher is read from the `Laminas\Cli\SymfonyEventDispatcher` service, the key laminas-cli uses, and must implement `Symfony\Component\EventDispatcher\EventDispatcherInterface`.
- The factories throw `Dot\Cli\Exception\InvalidConfigException` (an `Exception` and a PSR-11 `ContainerExceptionInterface`) instead of `Exception`.
- `ApplicationFactory::__invoke()` returns `Dot\Cli\Application`.
- The `MESSAGE_*` constants are typed (`public const string`).
- `config/autoload/cli.global.php` uses the command name as the key instead of `DemoCommand::getDefaultName()`, which Symfony Console 8 removed.
- `DemoCommand::$defaultName` is typed as `string`. `DemoCommand` keeps it and `configure()` as a demo of extending a command; Symfony Console reads the name and description from the `#[AsCommand]` attribute.
- Development tooling: PHPUnit 12 with strict `failOn*` flags, PHPStan level 8, GitHub Actions run PHP 8.3 to 8.6 with `actions/checkout@v7`, `actions/cache@v6` and `codecov/codecov-action@v7`, and the Laminas backward-compatibility check is disabled for this major version.
- Documentation for 4.0 is available in `docs/book/v4`, including an upgrade guide from 3.x.

### Added

- `Dot\Cli\Exception\ExceptionInterface`, `Dot\Cli\Exception\RuntimeException` and `Dot\Cli\Exception\InvalidConfigException`.
- `ApplicationFactory::EVENT_DISPATCHER` and `ApplicationFactory::LEGACY_EVENT_DISPATCHER`.
- `psr/container`, `symfony/console` and `symfony/event-dispatcher` as direct requirements.
- Tests with a minimal PSR-11 container and a real temporary directory, covering lock contention, aliases, abbreviations, quiet output and the event dispatcher keys.

### Deprecated

- The `Dot\Cli\Factory\SymfonyEventDispatcher` service key. Use `Laminas\Cli\SymfonyEventDispatcher`. It will be removed in 5.0.

### Removed

- PHP 8.2 support.
- `laminas/laminas-servicemanager` as a requirement. dot-cli only uses PSR-11; applications that use Laminas Service Manager require it themselves.
- `mikey179/vfsstream` as a development requirement. Lock tests use a real temporary directory.

### Fixed

- A running command could be started again through one of its aliases or an abbreviation of its name.
- A missing lock directory caused a `TypeError` in `Application::doRun()` and a second one in `Application::__destruct()`.
- `FileLocker::unlock()` caused a `TypeError` when called before `lock()` or twice.
- When `flock()` failed without reporting contention, the command ran without a lock.
- Calling `FileLocker::lock()` twice leaked a file handle and reported that a different process held the lock.
- `FileLocker::setDirPath(null)` and `FileLocker::getLockFilePath()` without a command name caused a `TypeError`.
- With `-q`, the lock error was not shown.
- An event dispatcher registered under the laminas-cli key was ignored.
- The shipped `config/autoload/cli.global.php` failed on Symfony Console 8.

## 3.11.1 - 2026-05-27

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- README badges.

## 3.11.0 - 2026-05-27

### Changed

- `laminas/laminas-cli` is required as `^1.15` (#49). This release was tagged as `3.11`.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.10.0 - 2025-10-29

### Changed

- Supported PHP versions are 8.2, 8.3, 8.4 and 8.5 (#47).

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- PHP 8.1 from the CI matrix (#47).

### Fixed

- Nothing

## 3.9.0 - 2025-02-28

### Changed

- Static analysis uses PHPStan instead of Psalm (#45).

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.8.1 - 2025-01-27

### Changed

- Psalm 6.x (#43).

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.8.0 - 2025-01-24

### Changed

- Nothing

### Added

- PHP 8.4 support (#40).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.7.0 - 2025-01-08

### Changed

- Nothing

### Added

- `laminas/laminas-servicemanager` 4.x support (#37).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- `docs/book/index.md` and README badges.

## 3.6.1 - 2024-07-04

### Changed

- The package is described as a wrapper on top of laminas-cli.

### Added

- Lock files documentation.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Continuous integration badge.
- Typo in a documented command.

## 3.6.0 - 2024-05-03

### Changed

- `mikey179/vfsstream` updated.

### Added

- Documentation for v3.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- `mkdocs.yml` and formatting reported by the QA checks.

## 3.5.0 - 2024-02-08

### Changed

- Removed PHP 8.1 support.

### Added

- CHANGELOG.md

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Command name issue.

## 3.4.2 - 2023-11-29

### Changed

- Nothing

### Added

- PHP 8.3 support.
- Tests, bringing code coverage to 100% (#20).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.4.1 - 2023-08-22

### Changed

- Nothing

### Added

- Codecov and its badge (#16, #18).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.4.0 - 2023-07-05

### Changed

- Nothing

### Added

- Unit tests, coding standard checks and static analysis (#13).

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.3.0 - 2023-03-29

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- `laminas/laminas-dependency-plugin`.

### Fixed

- Nothing

## 3.2.1 - 2023-03-09

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- The command that lists all commands (#7).

## 3.2.0 - 2022-04-06

### Changed

- `composer.json` and README updates.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.1.0 - 2022-02-03

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- The `laminas/laminas-cli` version constraint.

## 3.0.2 - 2021-08-13

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Issue #2.

## 3.0.1 - 2021-05-21

### Changed

- Minor improvements.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.0 - 2021-05-17

### Changed

- Nothing

### Added

- Initial 3.x release, based on laminas-cli.

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
