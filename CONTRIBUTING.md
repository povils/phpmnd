# How to contribute

Thanks for considering to contribute to `PHPMND`. While doing so please follow these guidelines:
 
 - You must follow the `PSR-2` coding standard. Please see [PSR-2](http://www.php-fig.org/psr/psr-2/) for more details.
 - You must ensure the coding standard compliance before committing or opening pull requests by running `composer cs-check` and if required `composer cs-fix` in the root directory of this repository. If one of these Composer scripts fails to run, please do a `composer update` and rerun it.
 - All non trivial features or bugfixes must have an associated issue for discussion. If you want to work on an issue that is already created, please leave a comment on it indicating that you are working on it.
 - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.
 - Add tests for features or bugfixes touching `src` code if you want to increase the chance of your contribution being merged.
 - You must use [feature / topic branches](https://git-scm.com/book/en/v2/Git-Branching-Branching-Workflows) to ease the merge of your contribution.
