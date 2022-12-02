# Metrics

A package of utilities for dealing with coverage metrics in PHP, and annotating
your source files therefrom.

## Installation

```shell
composer require logicbrush/metrics
```

## Command Line Functions

A number of metrics-related functions are accessible via the command line.

### Annotate

Use to annotate source code with metric information from a `coverage.xml` file.
Use with your CI process to keep your metrics in front of you as you develop.

```shell
php ./vendor/bin/metrics annotate «path to coverage.xml» «path to source file»
```

This will annotate your code with a `@Metrics` annotation, e.g:

```php
/**
 * You must have an existing docblock comment on your method for this to work.  
 * We won't add it for you.
 * 
 * @Metrics( crap = 10.2, uncovered = true )
 */
 public function someMethod() : void {
    ...
 }
 ```