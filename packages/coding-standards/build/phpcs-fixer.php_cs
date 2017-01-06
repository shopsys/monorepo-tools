<?php

$config = Symfony\CS\Config\Config::create()
	->level(Symfony\CS\FixerInterface::NONE_LEVEL)
	// list of all available fixers: https://github.com/FriendsOfPHP/PHP-CS-Fixer/
	->fixers([
		//'align_double_arrow', // opposite to unalign_double_arrow
		//'align_equals', // opposite to unalign_equals
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
		'ereg_to_preg',
		'extra_empty_lines',
		'function_call_space',
		'function_declaration',
		// 'header_comment', // header comments does not matter
		'include',
		//'indentation', // uses spaces instead of tabs
		'join_function',
		'line_after_namespace',
		'linefeed',
		'list_commas',
		//'long_array_syntax', // opposite to short_array_syntax
		'lowercase_constants',
		'lowercase_keywords',
		'method_argument_space',
		'multiline_array_trailing_comma',
		'multiline_spaces_before_semicolon',
		'multiple_use',
		'namespace_no_leading_whitespace',
		'newline_after_open_tag',
		'new_with_braces',
		//'no_blank_lines_after_class_opening', // we would like the exact opposite
		//'no_blank_lines_before_namespace', // there should be single blank line before namespace
		'no_empty_lines_after_phpdocs',
		'object_operator',
		'operators_spaces',
		'ordered_use',
		'parenthesis',
		'php_closing_tag',
		'php4_constructor',
		'phpdoc_indent',
		'phpdoc_no_access',
		'phpdoc_no_empty_return',
		'phpdoc_no_package',
		'phpdoc_order',
		//'phpdoc_params', // do not want vertically aligned phpdocs
		'phpdoc_scalar',
		//'phpdoc_separation', // do not want phpdoc annotations grouping
		//'phpdoc_short_description', // descriptons does not have to end with ".", "!" or "?"
		'phpdoc_trim',
		'phpdoc_type_to_var',
		//'phpdoc_var_to_type', // opposite to phpdoc_type_to_var
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
		'unalign_equals',
		'unary_operators_spaces',
		//'unused_use', // we use custom UnusedUseFixer
		'visibility',
		'whitespacy_lines',
	])
	->addCustomFixer(new ShopSys\CodingStandards\CsFixer\MissingButtonTypeFixer())
	->addCustomFixer(new ShopSys\CodingStandards\CsFixer\OrmJoinColumnRequireNullableFixer())
	->addCustomFixer(new ShopSys\CodingStandards\CsFixer\UnusedUseFixer());

// variable $path is available from include from FixCommand::execute()
if (!is_dir($path) && !is_file($path)) {
	$realpaths = [];

	foreach (explode(' ', trim($path)) as $filepath) {
		$splFileInfo = new \SplFileInfo($filepath);
		$realpaths[] = $splFileInfo->getRealPath();
	}

	$config->getFinder()
		->filter(
			function (\SplFileInfo $file) use ($realpaths) {
				return in_array($file->getRealPath(), $realpaths, true);
			}
		);

	// to ensure only relevant directories are searched let's find common directory of all file paths and use it as $path
	$commonRealpathParts = explode(DIRECTORY_SEPARATOR, $realpaths[0]);
	foreach ($realpaths as $realpath) {
		$realpathParts = explode(DIRECTORY_SEPARATOR, $realpath);
		foreach ($commonRealpathParts as $i => $commonRealpathPart) {
			if (!array_key_exists($i, $realpathParts) || $commonRealpathPart !== $realpathParts[$i]) {
				$commonRealpathParts = array_slice($commonRealpathParts, 0, $i);
				break;
			}
		}
	}

	$path = implode(DIRECTORY_SEPARATOR, $commonRealpathParts);
}

return $config;