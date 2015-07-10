<?php

$config = Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'blankline_after_open_tag',
		//'braces', // do not want open braces at new line
		'concat_with_spaces',
		//'concat_without_spaces', // concatenation operator should be surrounded by single space
		'double_arrow_multiline_whitespaces',
		'duplicate_semicolon',
		'elseif',
		//'empty_return', // rewrites "return null;" to "return;"
		'encoding',
		'eof_ending',
		'extra_empty_lines',
		'function_call_space',
		'function_declaration',
		'include',
		//'indentation', // uses spaces instead of tabs
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
		//'no_blank_lines_after_class_opening', // we would like the exact opposite
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
		//'phpdoc_params', // do not want vertically aligned phpdocs
		'phpdoc_scalar',
		//'phpdoc_separation', // do not want phpdoc annotations grouping
		//'phpdoc_short_description', // descriptons does not have to end with ".", "!" or "?"
		'phpdoc_trim',
		'phpdoc_type_to_var',
		'phpdoc_var_without_name',
		//'pre_increment', // post-increment is totally OK
		'psr0',
		'remove_leading_slash_use',
		'remove_lines_between_uses',
		//'return', // not every return looks good with empty line before it
		'self_accessor',
		'short_array_syntax',
		'short_tag',
		'single_array_no_trailing_comma',
		'single_blank_line_before_namespace',
		'single_line_after_imports',
		'single_quote',
		'spaces_before_semicolon',
		//'spaces_cast', // cast and variable should not be separated by a space
		'standardize_not_equal',
		'ternary_spaces',
		'trailing_spaces',
		'trim_array_spaces',
		'unalign_double_arrow',
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