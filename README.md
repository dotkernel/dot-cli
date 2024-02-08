# dot-cli

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-cli)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-cli/3.4.2)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/blob/3.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml/badge.svg?branch=3.0)](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-cli/graph/badge.svg?token=0DFCK2GUBT)](https://codecov.io/gh/dotkernel/dot-cli)

[![SymfonyInsight](https://insight.symfony.com/projects/b9489f03-14e3-441f-aefd-e3b549b4917e/big.svg)](https://insight.symfony.com/projects/b9489f03-14e3-441f-aefd-e3b549b4917e)

DotKernel component to build console applications based on [laminas-cli](https://github.com/laminas/laminas-cli).

### Requirements
- PHP >= 8.2
- laminas/laminas-servicemanager >= 3.11,
- laminas/laminas-cli >= 1.4


### Setup
#### 1. Install package
Run the following command in your application's root directory:

    composer require dotkernel/dot-cli

#### 2. Register ConfigProvider
Open your application's `config/config.php` and the following line under the _DK packages_ comment:

     Dot\Cli\ConfigProvider::class,

#### 3. Copy bootstrap file
Locate file `bin/cli.php` in this package, then copy it to your application's `bin/` directory.
This is the bootstrap file you will use to execute your commands with.

#### 4. Copy config file
Locate in this package the following file `config/autoload/cli.global.php` then copy it to your application's `config/autoload/` directory.
This is the config file you will add your commands to.


### Testing
Using the command line, go to your application's root directory, then type the following command:

    php /bin/cli.php

The output should look similar to this, containing information on how to start using dot-cli:
```text
DotKernel CLI 1.0.0

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help          Display help for a command
  list          List commands
 demo
  demo:command  Demo command description.
```
As shown in `config/autoload/cli.global.php`, dot-cli includes a demo command `demo:command` that will help you understand the basics of creating a new command.
For more information, see [laminas-cli documentation](https://docs.laminas.dev/laminas-cli/).

### Setting up as cronjob
```text
*   *   *   *   *   /opt/plesk/php/7.4/bin/php /var/www/vhosts/example.com/httpdocs/bin/cli.php demo:command -q
```
or
```text
*   *   *   *   *   cd /var/www/vhosts/example.com/httpdocs/bin && /opt/plesk/php/7.4/bin/php ./cli.php demo:command -q
```
Adapt the command to your specifications by replacing **7.4** with your PHP version and **example.com** with your domain name.

Note the **-q** (or **--quiet**) option at the end of the command - it serves as a flag to inform the Application that no output should be returned (unless it's an error).

## License
MIT
