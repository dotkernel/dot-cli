# Usage

Use `src/Command/DemoCommand.php` as an example when creating a new command.
Update the name and description in the `AsCommand` attribute as needed.
Also update the `$defaultName` property and the description set inside the `configure` method to match the `AsCommand` attribute.

## Running

Using the command line, go to your application's root directory, then type the following command:

```shell
php ./bin/cli.php demo:command
```
