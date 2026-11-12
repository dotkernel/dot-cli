# Testing

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
