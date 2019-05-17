<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use Doctrine\Common\Annotations\DocParser;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use SplFileInfo;
use Twig_Node;

class PhpFileExtractor implements FileVisitorInterface, NodeVisitor
{
    /** @access protected */
    const DEFAULT_MESSAGE_DOMAIN = 'messages';

    /**
     * @var \PhpParser\NodeTraverser
     */
    protected $traverser;

    /**
     * @var \Doctrine\Common\Annotations\DocParser
     */
    protected $docParser;

    /**
     * @var \JMS\TranslationBundle\Model\MessageCatalogue
     */
    protected $catalogue;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Translation\TransMethodSpecification[]
     */
    protected $transMethodSpecifications;

    /**
     * @var \PhpParser\Node|null
     */
    protected $previousNode;

    /**
     * @param \Doctrine\Common\Annotations\DocParser $docParser
     * @param \Shopsys\FrameworkBundle\Component\Translation\TransMethodSpecification[] $transMethodSpecifications
     */
    public function __construct(DocParser $docParser, array $transMethodSpecifications)
    {
        $this->docParser = $docParser;
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this);

        $this->transMethodSpecifications = [];
        foreach ($transMethodSpecifications as $transMethodSpecification) {
            $methodName = $this->getNormalizedMethodName($transMethodSpecification->getMethodName());
            $this->transMethodSpecifications[$methodName] = $transMethodSpecification;
        }
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param array $ast
     */
    public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast)
    {
        $this->file = $file;
        $this->catalogue = $catalogue;
        $this->traverser->traverse($ast);
    }

    /**
     * @param \PhpParser\Node $node
     */
    public function enterNode(Node $node)
    {
        if ($this->isTransMethodOrFuncCall($node)) {
            if (!$this->isIgnored($node)) {
                /* @var $node \PhpParser\Node\Expr */
                $messageId = $this->getMessageId($node);
                $domain = $this->getDomain($node);

                $message = new Message($messageId, $domain);
                $message->addSource(new FileSource((string)$this->file->getFilename(), $node->getLine()));

                $this->catalogue->add($message);
            }
        }

        $this->previousNode = $node;
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\FuncCall $node
     * @return string
     */
    protected function getMessageId($node)
    {
        $methodName = $this->getNormalizedMethodName($this->getNodeName($node));
        $messageIdArgumentIndex = $this->transMethodSpecifications[$methodName]->getMessageIdArgumentIndex();

        if (!isset($node->args[$messageIdArgumentIndex])) {
            throw new \Shopsys\FrameworkBundle\Component\Translation\Exception\MessageIdArgumentNotPresent();
        }

        return PhpParserNodeHelper::getConcatenatedStringValue($node->args[$messageIdArgumentIndex]->value, $this->file);
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\FuncCall $node
     * @return string
     */
    protected function getDomain($node)
    {
        $methodName = $this->getNormalizedMethodName($this->getNodeName($node));
        $domainArgumentIndex = $this->transMethodSpecifications[$methodName]->getDomainArgumentIndex();

        if ($domainArgumentIndex !== null && isset($node->args[$domainArgumentIndex])) {
            return PhpParserNodeHelper::getConcatenatedStringValue($node->args[$domainArgumentIndex]->value, $this->file);
        } else {
            return static::DEFAULT_MESSAGE_DOMAIN;
        }
    }

    /**
     * @param \PhpParser\Node $node
     * @return bool
     */
    protected function isTransMethodOrFuncCall(Node $node)
    {
        if ($node instanceof MethodCall || $node instanceof FuncCall) {
            try {
                $methodName = $this->getNormalizedMethodName($this->getNodeName($node));
            } catch (\Shopsys\FrameworkBundle\Component\Translation\Exception\ExtractionException $ex) {
                return false;
            }

            if (array_key_exists($methodName, $this->transMethodSpecifications)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node $node
     * @return bool
     */
    protected function isIgnored(Node $node)
    {
        foreach ($this->getAnnotations($node) as $annotation) {
            if ($annotation instanceof Ignore) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpParser\Node $node
     * @return \Doctrine\Common\Annotations\Annotation[]|\JMS\TranslationBundle\Annotation\Ignore[]
     */
    protected function getAnnotations(Node $node)
    {
        $docComment = $this->getDocComment($node);

        if ($docComment !== null) {
            return $this->docParser->parse($docComment->getText(), 'file ' . $this->file . ' near line ' . $node->getLine());
        }

        return [];
    }

    /**
     * @param \PhpParser\Node $node
     * @return \PhpParser\Comment\Doc|null
     */
    protected function getDocComment(Node $node)
    {
        $docComment = $node->getDocComment();

        if ($docComment === null) {
            if ($this->previousNode !== null) {
                $docComment = $this->previousNode->getDocComment();
            }
        }

        return $docComment;
    }

    /**
     * @param string $methodName
     * @return string
     */
    protected function getNormalizedMethodName($methodName)
    {
        return mb_strtolower($methodName);
    }

    /**
     * @param \PhpParser\Node $node
     * @return string
     */
    protected function getNodeName(Node $node)
    {
        if ($node instanceof MethodCall) {
            return (string)$node->name;
        } elseif ($node instanceof FuncCall && $node->name instanceof Name) {
            return (string)$node->name;
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Translation\Exception\ExtractionException('Unable to resolve node name');
        }
    }

    /**
     * @param array $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        return null;
    }

    /**
     * @param \PhpParser\Node $node
     */
    public function leaveNode(Node $node)
    {
        return null;
    }

    /**
     * @param array $nodes
     */
    public function afterTraverse(array $nodes)
    {
        return null;
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     */
    public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue)
    {
        return null;
    }

    /**
     * @param \SplFileInfo $file
     * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
     * @param \Twig_Node $ast
     */
    public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $ast)
    {
        return null;
    }
}
