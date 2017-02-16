<?php

namespace Shopsys\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
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
class ConstraintMessageExtractor implements FileVisitorInterface, NodeVisitor
{
    const CONSTRAINT_MESSAGE_DOMAIN = 'validators';

    /**
     * @var \PhpParser\NodeTraverser
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

    public function __construct()
    {
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this);
    }

    /**
     * @inheritdoc
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof New_) {
            if ($this->isConstraintClass($node->class) && count($node->args) > 0) {
                $this->extractMessagesFromOptions($node->args[0]->value);
            }
        }
    }

    /**
     * @param \PhpParser\Node $node
     * @return bool
     */
    private function isConstraintClass(Node $node)
    {
        return $node instanceof FullyQualified && is_subclass_of((string)$node, Constraint::class);
    }

    /**
     * @param \PhpParser\Node $optionsNode
     */
    private function extractMessagesFromOptions(Node $optionsNode)
    {
        if ($optionsNode instanceof Array_) {
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
     * @param \PhpParser\Node\Expr\ArrayItem $node
     * @return bool
     */
    private function isMessageOptionItem(ArrayItem $node)
    {
        return $node->key instanceof String_ && strtolower(substr($node->key->value, -7)) === 'message';
    }

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function afterTraverse(array $nodes)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $ast)
    {
        return null;
    }
}
