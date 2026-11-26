# Setting up as a cronjob

```text
*   *   *   *   *   php /var/www/vhosts/example.com/httpdocs/bin/cli.php demo:command -q
```

or

```text
*   *   *   *   *   cd /var/www/vhosts/example.com/httpdocs/bin && php ./cli.php demo:command -q
```

Adapt the command to your specifications by replacing **example.com** with your domain name.

The **-q** (or **--quiet**) option suppresses normal output, but errors are still written to stderr.
This includes the lock error shown when the previous run of the same command has not finished yet (see [Lock Files](lock-files.md)), so cron reports skipped runs.
Use **--silent** instead if you want no output at all.
