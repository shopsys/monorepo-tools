<?php

$config = Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'concat_with_spaces',
		'elseif',
		'extra_empty_lines',
		'function_call_space',
		'function_declaration',
		'line_after_namespace',
		'linefeed',
		'lowercase_constants',
		'lowercase_keywords',
		'method_argument_space',
		'multiline_array_trailing_comma',
		'multiple_use',
		'namespace_no_leading_whitespace',
		'new_with_braces',
		'object_operator',
		'operators_spaces',
		'ordered_use',
		'parenthesis',
		'php_closing_tag',
		'psr0',
		'remove_lines_between_uses',
		'short_array_syntax',
		'short_tag',
		'single_array_no_trailing_comma',
		'spaces_before_semicolon',
		'standardize_not_equal',
		'ternary_spaces',
		'trailing_spaces',
		'whitespacy_lines',
	])
	->addCustomFixer(new SS6\ShopBundle\Component\CsFixer\UnusedUseFixer());

// variable $path is available from include from FixCommand::execute()
if (!is_dir($path) && !is_file($path)) {
	$files = [];
	foreach (explode(' ', trim($path)) as $filepath) {
		$files[] = new \SplFileInfo($filepath);
	}
	$config->finder(new \ArrayIterator($files));
}

return $config;