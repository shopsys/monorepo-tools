<?php

namespace Shopsys\ShopBundle\Component\Translation;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use Shopsys\ShopBundle\Component\Translation\Exception\StringValueUnextractableException;
use SplFileInfo;

class PhpParserNodeHelper
{
    /**
     * @param \PhpParser\Node $node
     * @param \SplFileInfo $fileInfo
     * @return string
     */
    public static function getConcatenatedStringValue(Node $node, SplFileInfo $fileInfo)
    {
        if ($node instanceof String_) {
            return $node->value;
        }

        if ($node instanceof Concat) {
            return self::getConcatenatedStringValue($node->left, $fileInfo) . self::getConcatenatedStringValue($node->right, $fileInfo);
        }

        throw new StringValueUnextractableException(
            sprintf(
                'Can only extract the message ID or message domain from a scalar or concatenated string,'
                . ' but got "%s". Please refactor your code to make it extractable (in %s on line %d).',
                get_class($node),
                $fileInfo,
                $node->getLine()
            )
        );
    }
}
