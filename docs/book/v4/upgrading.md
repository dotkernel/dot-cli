# Upgrading from 3.x to 4.0

Version 4.0 keeps the 3.x configuration format and the public classes.
Most applications only need to update the `commands` keys in `config/autoload/cli.global.php` and require `laminas/laminas-servicemanager` themselves.

## Changes that can break your code

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 is no longer supported | Composer refuses to install 4.0 | Use PHP 8.3 or newer, or stay on 3.x |
| `laminas/laminas-servicemanager` is no longer required | `Class "Laminas\ServiceManager\ServiceManager" not found` if your application relied on dot-cli to install it | Run `composer require laminas/laminas-servicemanager` in your application |
| The shipped config no longer uses `DemoCommand::getDefaultName()` as a key | A config key such as `DemoCommand::getDefaultName()` fails with `Call to undefined method` on Symfony Console 8 | Use the command name as the key, see [below](#command-keys) |
| The event dispatcher service key is now `Laminas\Cli\SymfonyEventDispatcher` | None yet: the old key still works, but it is deprecated | Rename the service key, see [below](#event-dispatcher) |
| A service under the event dispatcher key must implement `Symfony\Component\EventDispatcher\EventDispatcherInterface` | `Dot\Cli\Exception\InvalidConfigException` when the application is created | Register a Symfony event dispatcher under that key |
| Aliases and abbreviations of a command share its lock | `demo:com` or an alias is rejected while `demo:command` runs | This is intended. Give commands that must run in parallel their own names |
| Lock errors are written to stderr and are shown with `-q` | Cron output now includes `The file lock is being held by a different process` | Use `--silent`, or redirect stderr |
| A missing or unwritable lock directory throws `Dot\Cli\Exception\RuntimeException` | Commands exit with code `1` and a message instead of a `TypeError` | Create the directory, or disable the locker |
| `FileLocker::getLockFilePath()` throws `Dot\Cli\Exception\RuntimeException` without a directory or a command name | Code that called it too early gets an exception instead of a `TypeError` | Set both values first |
| The factories throw `Dot\Cli\Exception\InvalidConfigException` instead of `Exception` | None for `catch (Exception)`: the new class extends it | Optionally catch the new class, or `Psr\Container\ContainerExceptionInterface` |
| `ApplicationFactory::__invoke()` returns `Dot\Cli\Application` instead of `Symfony\Component\Console\Application` | A subclass that overrides `__invoke()` with the old return type fails to compile | Narrow the return type in your subclass |
| The `MESSAGE_*` constants are typed (`public const string`) | A subclass that redeclares one of them without a type fails to compile | Add the `string` type to the redeclared constant |

## Command keys

Before:

```php
'commands' => [
    DemoCommand::getDefaultName() => DemoCommand::class,
],
```

After:

```php
'commands' => [
    'demo:command' => DemoCommand::class,
],
```

If one of your own commands still sets `protected static $defaultName`, move the name to the `#[AsCommand]` attribute.
Symfony Console 7.4 and 8 do not read the property, so it no longer names the command.
`DemoCommand` keeps it, typed as `string`, together with `configure()`, only to demonstrate both, see [Usage](usage.md).

## Event dispatcher

Before:

```php
'factories' => [
    'Dot\Cli\Factory\SymfonyEventDispatcher' => MyEventDispatcherFactory::class,
],
```

After:

```php
'factories' => [
    'Laminas\Cli\SymfonyEventDispatcher' => MyEventDispatcherFactory::class,
],
```

The old key is checked after the new one, and will be removed in 5.0.

## Laminas Service Manager

dot-cli only uses the PSR-11 container interface.
If your application builds its container with Laminas Service Manager, add it to your application's requirements:

```shell
composer require laminas/laminas-servicemanager
```

`Dot\Cli\ConfigProvider` still returns the same `factories` and `aliases`, so no configuration change is needed.
