Yurinskiy Validators
=====================================

This package provides validators for the [Symfony Validator component](http://symfony.com/doc/current/components/validator.html) 
to identifiers accepted in the Russian Federation (INN, SNILS, etc.).

## Installation

To install this package, add `yurinskiy/validators` to your composer.json:

```bash
composer require yurinskiy/validators
```

Now, [Composer][1] will automatically download all required files, and install them
for you.

## Requirements

You need at least PHP 8.2 and Symfony 5.4, mbstring is recommended but not required.

## Basic Usage

**Caution:**

> The password validators do not enforce that the field must have a value!
> To make a field "required" use the [NotBlank constraint](http://symfony.com/doc/current/reference/constraints/NotBlank.html)
> in combination with the password validator(s).

All examples assume you have the Composer autoloader already in your code,
see also [How to Install and Use the Symfony Components](http://symfony.com/doc/current/components/using_components.html)
for more information.

## License

This library is released under the [MIT license](LICENSE).

[1]: https://getcomposer.org/doc/00-intro.md