<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PHPParser_Node;
use PHPParser_Node_Expr_Array;
use PHPParser_Node_Expr_ArrayItem;
use PHPParser_Node_Expr_New;
use PHPParser_Node_Name_FullyQualified;
use PHPParser_Node_Scalar_String;
use PHPParser_NodeTraverser;
use PHPParser_NodeVisitor;
use PHPParser_NodeVisitor_NameResolver;
use SplFileInfo;
use Symfony\Component\Validator\Constraint;
use Twig_Node;

/**
 * Extracts custom messages from constraint constructors for translation.
 *
 * Examples:
 *     new Constraint\NotBlank([
 *         'message' => 'This message will be extracted into "validators" translation domain',
 *     ]),
 *     new Constraint\Length([
 *         'max' => 50,
 *         'minMessage' => 'Actually, every option ending with "message" will be extracted',
 *     ])
 */
class ConstraintMessageExtractor implements FileVisitorInterface, PHPParser_NodeVisitor {

    const CONSTRAINT_MESSAGE_DOMAIN = 'validators';

    /**
     * @var \PHPParser_NodeTraverser
     */
    private $traverser;

    /**
     * @var \JMS\TranslationBundle\Model\MessageCatalogue
     */
    private $catalogue;

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @param \PHPParser_NodeVisitor_NameResolver $nameResolverVisitor
     */
    public function __construct(PHPParser_NodeVisitor_NameResolver $nameResolverVisitor) {
        $this->traverser = new PHPParser_NodeTraverser();
        $this->traverser->addVisitor($nameResolverVisitor);
        $this->traverser->addVisitor($this);
    }

    /**
     * @inheritdoc
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast) {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @inheritdoc
     */
    public function enterNode(PHPParser_Node $node) {
        if ($node instanceof PHPParser_Node_Expr_New) {
            if ($this->isConstraintClass($node->class) && count($node->args) > 0) {
                $this->extractMessagesFromOptions($node->args[0]->value);
            }
        }
    }

    /**
     * @param \PHPParser_Node $node
     * @return bool
     */
    private function isConstraintClass(PHPParser_Node $node) {
        return $node instanceof PHPParser_Node_Name_FullyQualified && is_subclass_of((string)$node, Constraint::class);
    }

    /**
     * @param \PHPParser_Node $optionsNode
     */
    private function extractMessagesFromOptions(PHPParser_Node $optionsNode) {
        if ($optionsNode instanceof PHPParser_Node_Expr_Array) {
            foreach ($optionsNode->items as $optionItemNode) {
                if ($this->isMessageOptionItem($optionItemNode)) {
                    $messageId = PhpParserNodeHelper::getConcatenatedStringValue($optionItemNode->value, $this->file);

                    $message = new Message($messageId, self::CONSTRAINT_MESSAGE_DOMAIN);
                    $message->addSource(new FileSource($this->file->getFilename(), $optionItemNode->getLine()));

                    $this->catalogue->add($message);
                }
            }
        }
    }

    /**
     * @param \PHPParser_Node_Expr_ArrayItem $node
     * @return bool
     */
    private function isMessageOptionItem(PHPParser_Node_Expr_ArrayItem $node) {
        return $node->key instanceof PHPParser_Node_Scalar_String && strtolower(substr($node->key->value, -7)) === 'message';
    }

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes) {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(PHPParser_Node $node) {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function afterTraverse(array $nodes) {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue) {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $ast) {
        return null;
    }

}
