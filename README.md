# Metrics

A command line utility for making use of coverage metrics in PHP.  Chiefly,
allows you to update your source code with docblock annotations that show method
coverage/complexity.

## Installation

```shell
composer require logicbrush/metrics
```

## Usage

Use to annotate source code with metric information from a `coverage.xml` file,
like the ones you can generate with
[PHPUnit](https://docs.phpunit.de/en/10.5/code-coverage.html). Use with your CI
process to keep your metrics in front of you as you develop.

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