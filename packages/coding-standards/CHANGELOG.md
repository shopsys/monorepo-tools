# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

*Note: Changes are described since release 3.0.2.*

## [Unreleased]

### Added
- [#12] Added [EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard)

### Changed
- `OrmJoinColumnRequireNullableFixer` marked as *risky* (@sustmi)
- [#11] dropped support of PHP 7.0 (@vitek-rostislav)

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

[Unreleased]: https://github.com/shopsys/coding-standards/compare/v3.1.1...HEAD
[3.1.1]: https://github.com/shopsys/coding-standards/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/shopsys/coding-standards/compare/v3.0.2...v3.1.0
