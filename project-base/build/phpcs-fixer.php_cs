<?php

return Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'elseif',
		'function_call_space',
		'function_declaration',
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
		'short_tag',
		'single_array_no_trailing_comma',
		'spaces_before_semicolon',
		'standardize_not_equal',
		'trailing_spaces',
	]);