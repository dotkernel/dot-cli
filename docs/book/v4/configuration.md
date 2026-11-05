# Configuration

## Register ConfigProvider

After installation, register `dot-cli` in your project by adding the below line to your configuration aggregator (usually: `config/config.php`):

```php
Dot\Cli\ConfigProvider::class,
```

## Copy bootstrap file

Locate file `bin/cli.php` in this package, then copy it to your application's `bin/` directory.
This is the bootstrap file you will use to execute your commands with.

## Copy config file

Locate in this package the following file `config/autoload/cli.global.php` then copy it to your application's `config/autoload/` directory.
Add your commands under the `commands` key, using the command name as the key and the command class as the value:

```php
'commands' => [
    'demo:command'    => DemoCommand::class,
    'example:command' => ExampleCommand::class,
],
```

Use the name from the command's `#[AsCommand]` attribute.
Do not use `DemoCommand::getDefaultName()`: Symfony Console 8 removed that method, and Symfony Console 7.4 reports it as deprecated.

Command classes that are registered in the container are fetched from it.
Command classes that are not registered are instantiated without arguments.

## Custom event dispatcher

By default, the application uses a new `Symfony\Component\EventDispatcher\EventDispatcher`.
To use your own, register it in the container under the key `Laminas\Cli\SymfonyEventDispatcher`, the same key laminas-cli uses.
The service must implement `Symfony\Component\EventDispatcher\EventDispatcherInterface`.

```php
'dependencies' => [
    'factories' => [
        'Laminas\Cli\SymfonyEventDispatcher' => MyEventDispatcherFactory::class,
    ],
],
```

The 3.x key `Dot\Cli\Factory\SymfonyEventDispatcher` still works, but it is deprecated and will be removed in 5.0.
