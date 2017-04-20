# PHP Magic Number Detector (PHPMND)

`phpmnd` is a tool that **helps** you detect magic numbers in PHP code. By default 0 and 1 are not considered to be magic numbers.

## What is Magic Number?
Magic number is a numeric literal that is not defined as a constant that may change at a later stage, but that can be therefore hard to update. It is a bad programming practice of using numbers directly in source code without explanation. In most cases this makes programs harder to read, understand, and maintain. 

```php
class Foo 
{
    public function setPassword($password)
    {
         // don't do this
         if ($password > 7) {
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
         if ($password > self::MAX_PASSWORD_LENGTH) {
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


