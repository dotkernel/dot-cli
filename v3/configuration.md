# Configuration

## Register ConfigProvider

After installation, register `dot-cli` in your project by adding the below line to your configuration aggregator (usually: `config/config.php`):

```shell
Dot\Cli\ConfigProvider::class,
```

## Copy bootstrap file

Locate file `bin/cli.php` in this package, then copy it to your application's `bin/` directory.
This is the bootstrap file you will use to execute your commands with.

## Copy config file

Locate in this package the following file `config/autoload/cli.global.php` then copy it to your application's `config/autoload/` directory.
This is the config file you will add your commands under the `commands` key, as in the following example:

```php
'commands' => [
    DemoCommand::getDefaultName()    => DemoCommand::class,
    ExampleCommand::getDefaultName() => ExampleCommand::class,
],
```
