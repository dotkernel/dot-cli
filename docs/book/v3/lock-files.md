# Lock Files

When a command starts running, a lock file is created for that command.
The lock file ensures that the same command would not run before the previous one is done.
If a command was force stopped, locate the lock file in the configured directory and delete it to refresh the command.

## Configuration

Inside `config/autoload/cli.global.php` under the `FileLockerInterface::class` key the file locker can be disabled and the location of the lock files can be changed

    FileLockerInterface::class => [
        'enabled' => true,
        'dirPath' => getcwd() . '/data/lock',
    ],
