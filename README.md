# dot-cli

DotKernel component to build console applications based on laminas-cli

### Requirements
- PHP >= 7.4
- laminas/laminas-servicemanager >= 3.6,
- laminas/laminas-cli >= 1.0


### Setup
#### 1. Install package
Run the following command in your application's root directory:
```bash
$ composer require dotkernel/dot-cli
```

#### 2. Register ConfigProvider
Open your application's `config/config.php` and add `Dot\Cli\ConfigProvider::class,` under the _DK packages_ comment.

#### 3. Copy bootstrap file
Locate in this package the following file `bin/cli.php` then copy it to your application's `bin/` directory.
This is the bootstrap file you will use to execute your commands with.

#### 4. Copy config file
Locate in this package the following file `config/autoload/cli.global.php` then copy it to your application's `config/autoload/` directory.
This is the config file you will add your commands to.


### Testing
Using the command line, go to your application's root directory, then type the following command:
```bash
$ php /bin/cli.php
```
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
  demo-command  Demo command description.
  help          Display help for a command
  list          List commands
```
As shown in `config/autoload/cli.global.php`, dot-cli includes a demo command `demo-command` that will help you understand the basics of creating a new command.
For more information, see [laminas-cli documentation](https://docs.laminas.dev/laminas-cli/).

## License
MIT
