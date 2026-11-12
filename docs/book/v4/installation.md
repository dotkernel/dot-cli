# Installation

## Requirements

- PHP 8.3, 8.4, 8.5 or 8.6
- `laminas/laminas-cli` 1.15 or newer
- `symfony/console` and `symfony/event-dispatcher` 7.4 or 8.x
- A PSR-11 container that reads `factories` and `aliases` configuration, for example `laminas/laminas-servicemanager`

`dotkernel/dot-cli` no longer installs `laminas/laminas-servicemanager`.
If your application uses it, require it in your application's `composer.json`.

## Install

Install `dotkernel/dot-cli` by executing the following Composer command:

```shell
composer require dotkernel/dot-cli
```
