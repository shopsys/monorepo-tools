<?php

$config = PhpCsFixer\Config::create()
    // list of all available fixers: https://github.com/FriendsOfPHP/PHP-CS-Fixer/
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['align_double_arrow' => false, 'align_equals' => false],
        'blank_line_after_opening_tag' => true,
        'concat_space' => ['spacing' => 'one'],
        'ereg_to_preg' => true,
        'include' => true,
        'linebreak_after_opening_tag' => true,
        'new_with_braces' => true,
        'no_alias_functions' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => ['use', 'extra'],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_php4_constructor' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => ['type' => 'var'],
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'phpdoc_var_without_name' => true,
        'self_accessor' => true,
        'single_blank_line_before_namespace' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
    ])
    ->setRiskyAllowed(true)
    ->registerCustomFixers([
        new ShopSys\CodingStandards\CsFixer\MissingButtonTypeFixer(),
        new ShopSys\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer(),
        new ShopSys\CodingStandards\CsFixer\NoUnusedImportsFixer(),
    ]);

return $config;
