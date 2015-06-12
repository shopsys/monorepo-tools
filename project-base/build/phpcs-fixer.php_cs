<?php

$config = Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'blankline_after_open_tag',
		//'braces',
		'concat_with_spaces',
		//'concat_without_spaces',
		'double_arrow_multiline_whitespaces',
		'duplicate_semicolon',
		'elseif',
		//'empty_return',
		'encoding',
		'eof_ending',
		'extra_empty_lines',
		'function_call_space',
		'function_declaration',
		'include',
		//'indentation',
		'join_function',
		'line_after_namespace',
		'linefeed',
		'list_commas',
		'lowercase_constants',
		'lowercase_keywords',
		'method_argument_space',
		'multiline_array_trailing_comma',
		'multiple_use',
		'namespace_no_leading_whitespace',
		'new_with_braces',
		//'no_blank_lines_after_class_opening',
		'no_empty_lines_after_phpdocs',
		'object_operator',
		'operators_spaces',
		'ordered_use',
		'parenthesis',
		'php_closing_tag',
		'phpdoc_indent',
		'phpdoc_no_access',
		'phpdoc_no_empty_return',
		'phpdoc_no_package',
		//'phpdoc_params',
		'phpdoc_scalar',
		//'phpdoc_separation',
		//'phpdoc_short_description',
		'phpdoc_trim',
		'phpdoc_type_to_var',
		'phpdoc_var_without_name',
		//'pre_increment',
		'psr0',
		'remove_lines_between_uses',
		'short_array_syntax',
		'short_tag',
		'single_array_no_trailing_comma',
		'single_line_after_imports',
		'spaces_before_semicolon',
		'standardize_not_equal',
		'ternary_spaces',
		'trailing_spaces',
		'visibility',
		'whitespacy_lines',
	])
	->addCustomFixer(new SS6\ShopBundle\Component\CsFixer\MissingButtonTypeFixer())
	->addCustomFixer(new SS6\ShopBundle\Component\CsFixer\UnusedUseFixer());

// variable $path is available from include from FixCommand::execute()
if (!is_dir($path) && !is_file($path)) {
	$files = [];

	foreach (explode(' ', trim($path)) as $filepath) {
		if (strpos($filepath, '_generated') !== false) {
			continue;
		}

		$files[] = new \SplFileInfo($filepath);
	}

	$config->finder(new \ArrayIterator($files));
} else {
	$config->getFinder()
		->exclude('_generated');
}

return $config;