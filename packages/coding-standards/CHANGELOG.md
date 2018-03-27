# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

*Note: Changes are described since release 3.0.2.*

## [Unreleased]
- We are releasing the Shopsys Framework in version 7 and we are synchronizing versions because
  the Shopsys Framework and all packages are now developed together and are now same-version compatible.

## [4.0] - 2018-01-27

### Added
- composer script `run-all-checks` for easier testing of the package (@TomasVotruba)

### Changed
- `OrmJoinColumnRequireNullableFixer` marked as *risky* (@sustmi)
- [#11](https://github.com/shopsys/coding-standards/pull/11) dropped support of PHP 7.0 (@vitek-rostislav)
- [#12](https://github.com/shopsys/coding-standards/pull/12/) [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) is now used (@TomasVotruba)
    - the tool encapsulates PHP-CS-Fixer and PHP_CodeSniffer 
    - rules configuration is now unified in single file - [`easy-coding-standard.neon`](easy-coding-standard.neon)
    - the option `ignore-whitespace` for rules checking method and class length is not available anymore
        - the limits were increased to 550 (class length) and 60 (method length)
    
### Removed
- PHP Mess Detector (@TomasVotruba)
- line length sniff (@TomasVotruba)

## [3.1.1] - 2017-10-31
### Fixed
- enabled custom fixers (@vitek-rostislav)

## [3.1.0] - 2017-10-12
### Added
- This changelog (@vitek-rostislav)
- [Description of used coding standards rules](docs/description-of-used-coding-standards-rules.md) (@vitek-rostislav)
- New rules in [phpcs-fixer ruleset](build/phpcs-fixer.php_cs) (@TomasLudvik):
    - combine_consecutive_unsets
    - function_typehint_space
    - hash_to_slash_comment
    - lowercase_cast
    - native_function_casing
    - no_empty_comment
    - no_empty_phpdoc
    - no_spaces_around_offset
    - no_unneeded_control_parentheses
    - no_useless_return
    - no_whitespace_before_comma_in_array
    - non_printable_character
    - normalize_index_brace
    - phpdoc_annotation_without_dot
    - phpdoc_no_useless_inheritdoc
    - phpdoc_single_line_var_spacing
    - protected_to_private
    - semicolon_after_instruction
    - short_scalar_cast
    - space_after_semicolon
    - whitespace_after_comma_in_array

### Changed
- friendsofphp/php-cs-fixer upgraded from version 2.1 to version 2.3 (@TomasLudvik)
- [phpcs-fixer ruleset](build/phpcs-fixer.php_cs) (@vitek-rostislav)
    - replaced deprecated "hash_to_slash_comment" rule with "single_line_comment_style" rule
    - custom NoUnusedImportsFixer replaced with standard "no_unused_imports" rule

### Deleted
- Redundant rules which were already covered by other rules (@vitek-rostislav)

[Unreleased]: https://github.com/shopsys/coding-standards/compare/v4.0.0...HEAD
[4.0]: https://github.com/shopsys/coding-standards/compare/v3.1.1...v4.0.0
[3.1.1]: https://github.com/shopsys/coding-standards/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/shopsys/coding-standards/compare/v3.0.2...v3.1.0
