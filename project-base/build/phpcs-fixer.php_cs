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
		'ordered_use',
		'parenthesis',
		'php_closing_tag',
		'psr0',
		'short_tag',
		'trailing_spaces',
	]);