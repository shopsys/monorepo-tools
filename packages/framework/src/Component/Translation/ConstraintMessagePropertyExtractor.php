<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use SplFileInfo;
use Symfony\Component\Validator\Constraint;
use Twig_Node;

/**
 * Extracts messages from public properties (with names ending "message") of custom constraints for translation.
 *
 * Example:
 *     class MyConstraint extends Constraint
 *     {
 *         public $message = 'This value will be extracted.';
 *
 *         public $otherMessage = 'This value will also be extracted.';
 *
 *         public $differentProperty = 'This value will not be extracted (not a message).';
 *     }
 */
class ConstraintMessagePropertyExtractor implements FileVisitorInterface, NodeVisitor
{
    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $traverser;

    /**
     * @var \JMS\TranslationBundle\Model\MessageCatalogue
     */
    protected $catalogue;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var bool
     */
    protected $isInsideConstraintClass = null;

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
        if ($node instanceof Class_) {
            $this->isInsideConstraintClass = $this->isConstraintClass($node);
        }

        if ($node instanceof Property && $node->isPublic() && $this->isInsideConstraintClass) {
            $this->extractMessagesFromProperty($node);
        }
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->isInsideConstraintClass = false;
        }
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     * @return bool
     */
    protected function isConstraintClass(Class_ $node)
    {
        return is_subclass_of((string)$node->namespacedName, Constraint::class);
    }

    /**
     * @param \PhpParser\Node\Stmt\Property $node
     */
    protected function extractMessagesFromProperty(Property $node)
    {
        foreach ($node->props as $propertyProperty) {
            if ($this->isMessagePropertyProperty($propertyProperty)) {
                $messageId = PhpParserNodeHelper::getConcatenatedStringValue($propertyProperty->default, $this->file);

                $message = new Message($messageId, ConstraintMessageExtractor::CONSTRAINT_MESSAGE_DOMAIN);
                $message->addSource(new FileSource($this->file->getFilename(), $propertyProperty->getLine()));

                $this->catalogue->add($message);
            }
        }
    }

    /**
     * @param \PhpParser\Node\Stmt\PropertyProperty $node
     * @return bool
     */
    protected function isMessagePropertyProperty(PropertyProperty $node)
    {
        return strtolower(substr($node->name, -7)) === 'message';
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
