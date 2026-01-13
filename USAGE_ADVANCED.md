# Advanced Usage Guide for PHPMND

This guide covers advanced configuration, extension development, and integration patterns for PHPMND.

## Table of Contents

1. [Configuration](#configuration)
2. [Custom Extensions](#custom-extensions)
3. [CI/CD Integration](#cicd-integration)
4. [Best Practices](#best-practices)
5. [Troubleshooting](#troubleshooting)

## Configuration

### Configuration File (.phpmnd.xml)

Create a `.phpmnd.xml` file in your project root:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <directories>
        <directory>src</directory>
        <directory>app</directory>
    </directories>
    
    <extensions>
        <extension>Povils\PHPMND\Extension\OperationExtension</extension>
        <extension>Povils\PHPMND\Extension\ReturnExtension</extension>
    </extensions>
    
    <exclude-directories>
        <directory>vendor</directory>
        <directory>tests</directory>
    </exclude-directories>
    
    <strings>true</strings>
</config>
```

### Command Line Options

```bash
# Scan specific directories
phpmnd src/ tests/

# Use custom configuration file
phpmnd --config=custom.xml

# Generate XML report
phpmnd --report-xml=report.xml

# Include strings in detection
phpmnd --strings

# Exclude certain files
phpmnd --exclude=tests/fixtures
```

## Custom Extensions

### Creating an Extension

Extensions allow you to define custom rules for detecting magic numbers in different contexts.

```php
<?php

namespace MyProject\Detection;

use PhpParser\Node;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\HintList;

class CustomExtension implements Extension
{
    public function apply(Node $node, HintList $hints): void
    {
        // Check for specific node types
        if ($node instanceof Node\Scalar\LNumber) {
            // Add hint for magic number detection
            if ($node->value > 100) {
                $hints->add('Custom magic number detected: ' . $node->value);
            }
        }
    }
}
```

### Registering Custom Extensions

Modify `src/Container.php` to register your extension:

```php
$container['extensions'] = function() {
    return [
        new OperationExtension(),
        new ReturnExtension(),
        new MyProject\Detection\CustomExtension(), // Your extension
    ];
};
```

### Understanding Node Types

Common node types to detect magic numbers:

- `Node\Scalar\LNumber` - Integer literals
- `Node\Scalar\DNumber` - Float literals
- `Node\Scalar\String_` - String literals
- `Node\Expr\Array_` - Array elements
- `Node\Expr\BinaryOp` - Binary operations with constants

## CI/CD Integration

### GitHub Actions

Create `.github/workflows/phpmnd.yml`:

```yaml
name: PHPMND Detection

on: [push, pull_request]

jobs:
  phpmnd:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
      
      - name: Install dependencies
        run: composer install
      
      - name: Run PHPMND
        run: vendor/bin/phpmnd . --report-xml=report.xml
      
      - name: Upload report
        if: always()
        uses: actions/upload-artifact@v2
        with:
          name: phpmnd-report
          path: report.xml
```

### GitLab CI

Create `.gitlab-ci.yml`:

```yaml
phpmnd:
  stage: test
  image: php:7.4
  script:
    - composer install
    - vendor/bin/phpmnd . --report-xml=report.xml
  artifacts:
    reports:
      sast: report.xml
```

## Best Practices

### 1. Define Magic Number Thresholds

Use constants for frequently repeated numbers:

```php
class UserService
{
    const MIN_PASSWORD_LENGTH = 8;
    const MAX_LOGIN_ATTEMPTS = 5;
    const SESSION_TIMEOUT = 3600;
    
    public function validatePassword(string $password): bool
    {
        return strlen($password) >= self::MIN_PASSWORD_LENGTH;
    }
}
```

### 2. Use Named Constants in Business Logic

```php
// Bad
if ($user->age > 18) {
    // ...
}

// Good
const LEGAL_AGE = 18;
if ($user->age > self::LEGAL_AGE) {
    // ...
}
```

### 3. Document Exception Cases

Add PHPDoc comments explaining magic numbers when necessary:

```php
/**
 * Calculate interest rate
 * Magic number 0.05 represents 5% annual interest rate for savings accounts
 */
public function calculateInterest(float $amount): float
{
    return $amount * 0.05;
}
```

### 4. Configure Exclusions Properly

Use configuration file to exclude known false positives:

```xml
<exclude-directories>
    <directory>tests/fixtures</directory>
    <directory>vendor</directory>
    <directory>migration</directory>
</exclude-directories>
```

## Troubleshooting

### Issue: False Positives

**Problem**: PHPMND reports magic numbers that are actually constants or intentional.

**Solution**: 
- Define constants instead of using literals
- Use custom configuration to exclude specific files
- Create an extension to whitelist specific patterns

### Issue: Performance

**Problem**: PHPMND is slow on large codebases.

**Solution**:
- Limit scan directories: `phpmnd src/`
- Exclude vendor and test directories
- Run parallel scans on different directories

### Issue: Integration Errors

**Problem**: PHPMND fails to parse certain PHP files.

**Solution**:
- Check PHP version compatibility (requires 7.4+)
- Ensure files have valid PHP syntax
- Check file permissions and readability

## Examples

### Example: Detecting Magic Numbers in Config

```php
// config.php
class Config
{
    public static $settings = [
        'max_connections' => 100,    // Magic number
        'timeout' => 30,             // Magic number
        'retry_count' => 3,          // Magic number
    ];
}
```

Run PHPMND to identify these:
```bash
phpmnd src/config.php --report-xml=report.xml
```

### Example: Custom Extension for API Version

```php
class ApiVersionExtension implements Extension
{
    private $allowedVersions = [1, 2, 3];
    
    public function apply(Node $node, HintList $hints): void
    {
        if ($node instanceof Node\Expr\FuncCall 
            && $node->name->toString() === 'setApiVersion') {
            if ($node->args[0]->value instanceof Node\Scalar\LNumber) {
                $version = $node->args[0]->value->value;
                if (!in_array($version, $this->allowedVersions)) {
                    $hints->add("Unknown API version: $version");
                }
            }
        }
    }
}
```

## Contributing

Found an issue or have a suggestion? Please contribute!

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Add tests for your changes
3. Submit a pull request

See [CONTRIBUTING.md](CONTRIBUTING.md) for more details.
