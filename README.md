# Metrics

A command line utility for making better use of coverage metrics in PHP.
Chiefly, allows you to update your source code with docblock annotations that
present method coverage/complexity. Use with your CI process to keep your
metrics in front of you as you develop.

## Installation

```shell
composer require --dev logicbrush/metrics
```

## Usage

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
