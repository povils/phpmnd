# PHP Magic Number Detector (PHPMND)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/povils/phpmnd/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/povils/phpmnd/?branch=master)
[![License](https://poser.pugx.org/povils/phpmnd/license)](https://packagist.org/packages/povils/phpmnd)
[![Build Status](https://travis-ci.org/povils/phpmnd.svg?branch=master)](https://travis-ci.org/povils/phpmnd)

`phpmnd` is a tool that **helps** you detect magic numbers in PHP code. By default 0 and 1 are not considered to be magic numbers.

## What is Magic Number?
Magic number is a numeric literal that is not defined as a constant that may change at a later stage, but that can be therefore hard to update. It is a bad programming practice of using numbers directly in source code without explanation. In most cases this makes programs harder to read, understand, and maintain. 

```php
class Foo 
{
    public function setPassword($password)
    {
         // don't do this
         if (mb_strlen($password) > 7) {
              throw new InvalidArgumentException("password");
         }
    }
}
```
This should be refactored to:
```php
class Foo 
{
    const MAX_PASSWORD_LENGTH = 7; // not const SEVEN = 7 :)
    
    public function setPassword($password)
    {
         if (mb_strlen($password) > self::MAX_PASSWORD_LENGTH) {
              throw new InvalidArgumentException("password");
         }
    }
}
```
It improves readability of the code and it's easier to maintain.
Of course not every literal number is magic number.
```php
    $is_even = $number % 2 === 0
```
Surely number 2 is not a magic number.

***My rule of thumb:***
```
If the number came from business specs and is used directly - it is a magic number.
```
## Installation

### Composer

You can add this tool as a local, per-project, development-time dependency to your project using [Composer](https://getcomposer.org/):

    $ composer require --dev povils/phpmnd

You can then invoke it using the `vendor/bin/phpmnd` executable.

##### Globally
 To install globally:

```
    $ composer global require povils/phpmnd
```

Then make sure you have the global Composer binaries directory in your ``PATH``. Example for some Unix systems:

```
    $ export PATH="$PATH:$HOME/.composer/vendor/bin"
```
    
## Usage Example

Basic usage:

```
$ phpmnd wordpress --ignore-numbers=2,-1 --ignore-funcs=round,sleep --exclude=tests --progress --extensions=default_parameter,assign,argument
```

The ``--ignore-numbers`` option will exclude numbers from code analysis.

The ``--ignore-funcs`` option will exclude functions from code analysis when using "argument" extension.

The ``--exclude`` option will exclude a directory from code analysis (must be relative to source) (multiple values allowed)

The ``--exclude-path`` option will exclude path from code analysis (must be relative to source) (multiple values allowed)

The ``--exclude-name`` option will exclude file from code analysis (multiple values allowed)

The ``--progress`` option will display progress bar.

The ``--strings`` option will include strings literal search in code analysis.

The ``--ignore-strings`` option will exclude strings from code analysis when using "strings" option.

The ``--extensions`` option lets you extend code analysis (extensions must be separated by a comma).

**By default it analyses conditions, return statements and switch cases.**

Choose from the list of available extensions:

* **argument**
	```php
		round($number, 4);
	```
* **array**
 	```php
		$array = [200, 201];
	```
* **assign**
    ```php
		$var = 10;
	```
* **default_parameter**
    ```php
		function foo($default = 3);
	```
* **operation**
    ```php
		$bar = $foo * 20;
	```
* **property**
    ```php
		private $bar = 10;
	```
 
 I would recommend clean up code using default extension before using these extensions.
