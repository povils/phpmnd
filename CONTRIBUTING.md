# How to contribute

Thanks for considering to contribute to `PHPMND`. While doing so please follow these guidelines:
 
 - You must follow the `PSR-2` coding standard. Please see [PSR-2](http://www.php-fig.org/psr/psr-2/) for more details.
 - You must ensure the coding standard compliance before committing or opening pull requests by running `composer cs-check` and if required `composer cs-fix` in the root directory of this repository. If one of these Composer scripts fails to run, please do a `composer update` and rerun it. In case you miss this manual check, Travis CI will enforce it and you should fix the detected coding standard violations.
 - All non trivial features or bugfixes must have an associated issue for discussion. If you want to work on an issue that is already created, please leave a comment on it indicating that you are working on it.
 - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.
 - Add tests for features or bugfixes touching `src` code if you want to increase the chance of your contribution being merged.
 - You must use [feature / topic branches](https://git-scm.com/book/en/v2/Git-Branching-Branching-Workflows) to ease the merge of your contribution.

## Getting Started

### Setup Development Environment

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/phpmnd.git
   cd phpmnd
   ```
3. Create a feature branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. Install dependencies:
   ```bash
   composer install
   ```

### Testing Your Changes

Run the full test suite before submitting:
```bash
composer test
composer cs-check
```

To fix coding standard violations automatically:
```bash
composer cs-fix
```

## Creating Custom Extensions

To extend PHPMND with custom magic number detection:

1. Create a new class implementing `ExtensionInterface`:
```php
namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use Povils\PHPMND\HintList;

class CustomExtension implements Extension
{
    public function apply(Node $node, HintList $hints): void
    {
        // Your custom detection logic here
    }
}
```

2. Register in `Container.php` or configuration
3. Add corresponding tests in `tests/Extension/`

## Commit Message Convention

Follow this format:
```
[TYPE] Brief description of changes

- Detailed point 1
- Detailed point 2
```

Types: `[FEATURE]`, `[FIX]`, `[REFACTOR]`, `[DOCS]`, `[TEST]`, `[CHORE]`

Example:
```
[FEATURE] Add syntax error detection to exception handling

- New fromSyntaxError() method for line-specific errors
- New fileNotFound() method for missing files
- Improved error messages with context
```
