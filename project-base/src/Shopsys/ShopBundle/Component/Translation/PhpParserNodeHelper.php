<?php

namespace Shopsys\ShopBundle\Component\Translation;

use PHPParser_Node;
use PHPParser_Node_Expr_Concat;
use PHPParser_Node_Scalar_String;
use SplFileInfo;
use Shopsys\ShopBundle\Component\Translation\Exception\StringValueUnextractableException;

class PhpParserNodeHelper {

	/**
	 * @param \PHPParser_Node $node
	 * @param \SplFileInfo $fileInfo
	 * @return string
	 */
	public static function getConcatenatedStringValue(PHPParser_Node $node, SplFileInfo $fileInfo) {
		if ($node instanceof PHPParser_Node_Scalar_String) {
			return $node->value;
		}

		if ($node instanceof PHPParser_Node_Expr_Concat) {
			return self::getConcatenatedStringValue($node->left, $fileInfo) . self::getConcatenatedStringValue($node->right, $fileInfo);
		}

		throw new StringValueUnextractableException(
			sprintf('Can only extract the message ID or message domain from a scalar or concatenated string,'
				. ' but got "%s". Please refactor your code to make it extractable (in %s on line %d).',
			get_class($node), $fileInfo, $node->getLine())
		);
	}

}
