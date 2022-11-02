# Metrics

A package of utilities for dealing with coverage metrics.

## Installation

```shell
composer require logicbrush/metrics
```

## Command Line Functions

A number of metrics-related functions are accessible via the command line.

### Annotate

Use to annotate source code with metric information.  Use with your CI process
to keep your metrics in front of you as you develop.

```shell
php ./vendor/bin/metrics annotate «path to coverage.xml» «path to source file»
```