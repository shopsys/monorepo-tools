<?php

return Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	->fixers([
		'elseif',
		'function_call_space',
		'function_declaration',
//		'line_after_namespace',
//		'linefeed',
		'lowercase_constants',
		'lowercase_keywords',
		'method_argument_space',
		'multiple_use',
		'ordered_use',
		'parenthesis',
		'php_closing_tag',
		'psr0',
		'short_tag',
		'trailing_spaces',
	]);