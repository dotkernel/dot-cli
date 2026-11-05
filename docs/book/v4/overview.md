# Overview

> [!IMPORTANT]
> dot-cli is a wrapper on top of [laminas-cli](https://github.com/laminas/laminas-cli)

`dotkernel/dot-cli` builds a console application from your application's PSR-11 container.
Commands are registered in configuration, and a file lock stops the same command from running twice at the same time.

The factories only depend on `psr/container`, so the package works with any PSR-11 container that reads `factories` and `aliases` configuration, for example Laminas Service Manager.

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-cli)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-cli/4.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-cli)](https://github.com/dotkernel/dot-cli/blob/4.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-cli/actions/workflows/continuous-integration.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-cli/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-cli/graph/badge.svg?token=0DFCK2GUBT)](https://codecov.io/gh/dotkernel/dot-cli)
[![PHPStan](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-cli/actions/workflows/static-analysis.yml)
