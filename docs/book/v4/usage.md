# Usage

Use `src/Command/DemoCommand.php` as an example when creating a new command.
Set the name and description in the `#[AsCommand]` attribute: Symfony Console reads them from there, so a command needs neither a `$defaultName` property nor a `configure()` method to set them.

`DemoCommand` also implements `configure()` to show where a command can be extended by chaining methods, for example `setHelp()`, `addArgument()` and `addOption()`.
For the demo, its `configure()` sets the same name and description as the attribute.
Values set in `configure()` override the attribute, so if you keep both in your own command, keep them identical.
Symfony Console 7.4 and 8 do not read the `$defaultName` property: it is only there for the demo.

```php
<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:example',
    description: 'Example command description.',
)]
class ExampleCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from app:example');

        return Command::SUCCESS;
    }
}
```

Then register it in `config/autoload/cli.global.php`:

```php
'commands' => [
    'app:example' => App\Command\ExampleCommand::class,
],
```

## Running

Using the command line, go to your application's root directory, then type the following command:

```shell
php ./bin/cli.php demo:command
```
