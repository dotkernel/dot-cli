# dot-cli

Dotkernel component to build console applications based on [laminas-cli](https://github.com/laminas/laminas-cli), for any PSR-11 container.

> dot-cli is a wrapper on top of [laminas-cli](https://github.com/laminas/laminas-cli)

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-cli/.

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-cli)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-cli/4.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/blob/4.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-cli/actions/workflows/continuous-integration.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-cli/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-cli/graph/badge.svg?token=0DFCK2GUBT)](https://codecov.io/gh/dotkernel/dot-cli)
[![PHPStan](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml)

## Versions

| Version | Status | PHP | Documentation |
| --- | --- | --- | --- |
| 4.x | Active | 8.3, 8.4, 8.5, 8.6 | https://docs.dotkernel.org/dot-cli/v4/overview/ |
| 3.x | Security fixes only | 8.2, 8.3, 8.4, 8.5 | https://docs.dotkernel.org/dot-cli/v3/overview/ |

Upgrading from 3.x? Read the [upgrade guide](https://docs.dotkernel.org/dot-cli/v4/upgrading/).

## Requirements

- **PHP**: 8.3, 8.4, 8.5 or 8.6
- **laminas/laminas-cli**: >= 1.15
- **symfony/console** and **symfony/event-dispatcher**: 7.4 or 8.x
- A PSR-11 container that reads `factories` and `aliases` configuration, for example `laminas/laminas-servicemanager` (require it in your application)

## Setup

### 1. Install package

Run the following command in your application's root directory:

```shell
composer require dotkernel/dot-cli
```

### 2. Register ConfigProvider

Open your application's `config/config.php` and the following line under the _DK packages_ comment:

```php
Dot\Cli\ConfigProvider::class,
```

### 3. Copy bootstrap file

Locate file `bin/cli.php` in this package, then copy it to your application's `bin/` directory.
This is the bootstrap file you will use to execute your commands with.

### 4. Copy config file

Locate in this package the following file `config/autoload/cli.global.php` then copy it to your application's `config/autoload/` directory.
This is the config file you will add your commands to, using the command name as the key:

```php
'commands' => [
    'demo:command' => Dot\Cli\Command\DemoCommand::class,
],
```

The lock files are written to `data/lock` by default. Make sure the directory exists and is writable.

## Testing

Using the command line, go to your application's root directory, then type the following command:

```shell
php ./bin/cli.php
```

The output should look similar to this, containing information on how to start using dot-cli:

```text
Dotkernel CLI 1.0.0

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
      --silent          Do not output any message
  -q, --quiet           Only errors are displayed. All other output is suppressed
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  completion    Dump the shell completion script
  help          Display help for a command
  list          List commands
 demo
  demo:command  Demo command description.
```

As shown in `config/autoload/cli.global.php`, dot-cli includes a demo command `demo:command` that will help you understand the basics of creating a new command.
For more information, see [laminas-cli documentation](https://docs.laminas.dev/laminas-cli/).

## Setting up as a cronjob

```text
*   *   *   *   *   php /var/www/vhosts/example.com/httpdocs/bin/cli.php demo:command -q
```

or

```text
*   *   *   *   *   cd /var/www/vhosts/example.com/httpdocs/bin && php ./cli.php demo:command -q
```

Adapt the command to your specifications by replacing **example.com** with your domain name.

The **-q** (or **--quiet**) option suppresses normal output, but errors are still written to stderr, including the lock error shown when the previous run of the same command has not finished yet.
Use **--silent** instead if you want no output at all.

## License

MIT
