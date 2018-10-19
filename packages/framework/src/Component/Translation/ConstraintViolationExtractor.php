<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitor\NameResolver;
use SplFileInfo;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Twig_Node;

/**
 * Extracts custom message from constraint callback function.
 *
 * Examples:
 *     Working example
 *     public function workingExample(ExecutionContextInterface $context)
 *      {
 *          $context->addViolation('This message will be extracted into "validators" translation domain');
 *      }
 *
 *      non-working example
 *      public function nonWorkingExample(ExecutionContextInterface $context)
 *      {
 *          $message = 'This message will be not extracted into "validators" translation domain';
 *          $context->addViolation($message);
 *      }
 */
class ConstraintViolationExtractor implements FileVisitorInterface, NodeVisitor
{
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

    /**
     * @var string[]
     */
    private $currentExecutionContextVariableNames;

    public function __construct()
    {
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this);
        $this->currentExecutionContextVariableNames = [];
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
        if ($node instanceof ClassMethod) {
            $this->setCurrentExecutionContextVariableNamesFromNode($node);
        } elseif ($node instanceof MethodCall && $this->isAddViolationMethodCall($node)) {
            $this->extractMessage($node);
        }
    }

    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    private function setCurrentExecutionContextVariableNamesFromNode(ClassMethod $node)
    {
        $this->currentExecutionContextVariableNames = [];
        foreach ($node->getParams() as $parameter) {
            if ($this->isParameterExecutionContextInterfaceSubclass($parameter)) {
                $this->currentExecutionContextVariableNames[] = $parameter->name;
            }
        }
    }

    /**
     * @param \PhpParser\Node\Param $parameter
     * @return string
     */
    private function isParameterExecutionContextInterfaceSubclass(Node\Param $parameter)
    {
        if ($parameter->type instanceof FullyQualified) {
            $fullyQualifiedName = implode('\\', $parameter->type->parts);

            return $fullyQualifiedName === ExecutionContextInterface::class
                || is_subclass_of($fullyQualifiedName, ExecutionContextInterface::class);
        }

        return false;
    }

    /**
     * @param \PhpParser\Node $node
     * @return bool
     */
    private function isAddViolationMethodCall(Node $node): bool
    {
        return $node->var instanceof Variable
            && in_array($node->var->name, $this->currentExecutionContextVariableNames, true)
            && $node->name === 'addViolation';
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $methodCall
     */
    private function extractMessage(MethodCall $methodCall)
    {
        $firstArgumentWithMessage = reset($methodCall->args);
        if ($firstArgumentWithMessage->value instanceof String_) {
            $messageId = $firstArgumentWithMessage->value->value; // value with translatable message

            $message = new Message($messageId, ConstraintMessageExtractor::CONSTRAINT_MESSAGE_DOMAIN);
            $message->addSource(new FileSource($this->file->getFilename(), $firstArgumentWithMessage->getLine()));

            $this->catalogue->add($message);
        }
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
        if ($node instanceof ClassMethod) {
            $this->currentExecutionContextVariableNames = [];
        }
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
