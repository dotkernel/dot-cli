# Usage

Use `src/Command/DemoCommand.php` as an example when creating a new command.
Set the name and description in the `#[AsCommand]` attribute: the command needs no `$defaultName` property and no `configure()` call to set them.

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
