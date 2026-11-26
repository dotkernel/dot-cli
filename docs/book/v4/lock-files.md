# Lock Files

When a command starts running, it takes an exclusive lock on a lock file named after the command, for example `command-demo-command.lock`.
While the lock is held, another run of the same command stops with exit code `1` and the message `The file lock is being held by a different process`.

The lock is released when the command finishes.
If the process is killed, the operating system releases the lock, and the next run starts normally.
The lock file itself stays in the directory, which is expected: there is no need to delete it.

The lock uses the command's real name, so its aliases and abbreviations share one lock.
For example, while `demo:command` is running, `demo:com` is rejected too.
Unknown commands are not locked.

## Configuration

Inside `config/autoload/cli.global.php` under the `FileLockerInterface::class` key the file locker can be disabled and the location of the lock files can be changed.

```php
FileLockerInterface::class => [
    'enabled' => true,
    'dirPath' => getcwd() . '/data/lock',
],
```

The directory in `dirPath` must exist and be writable by the user that runs the commands.
If it does not, every command fails with exit code `1` and a message like `The lock directory "..." does not exist or is not writable.`
Create it before running commands:

```shell
mkdir -p data/lock
```

## Errors

Lock errors are written to the error output (stderr), and they are shown even with the `-q` (`--quiet`) option.
Use `--silent` to suppress them.
