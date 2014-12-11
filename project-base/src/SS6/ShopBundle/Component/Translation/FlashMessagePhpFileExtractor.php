<?php

namespace SS6\ShopBundle\Component\Translation;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use PHPParser_Node;
use PHPParser_Node_Expr_Concat;
use PHPParser_Node_Expr_MethodCall;
use PHPParser_Node_Scalar_String;
use PHPParser_NodeTraverser;
use PHPParser_NodeVisitor;
use SplFileInfo;
use SS6\ShopBundle\Component\Translation\Exception\InvalidTranslationIdException;
use Twig_Node;

class FlashMessagePhpFileExtractor implements FileVisitorInterface, PHPParser_NodeVisitor {

	const MESSAGE_DOMAIN = 'messages';

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

	public function __construct() {
		$this->traverser = new PHPParser_NodeTraverser();
		$this->traverser->addVisitor($this);
	}

	/**
	 * @param \SplFileInfo $file
	 * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
	 * @param array $ast
	 */
	public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast) {
		$this->file = $file;
		$this->catalogue = $catalogue;
		$this->traverser->traverse($ast);
	}

	/**
	 * @param \PHPParser_Node $node
	 */
	public function enterNode(PHPParser_Node $node) {
		if ($this->isFlashMessageAddMethodCall($node)) {
			/* @var $node \PHPParser_Node_Expr_MethodCall */
			$translationId = $this->getTranslationId($node->args[0]->value);

			$message = new Message($translationId, self::MESSAGE_DOMAIN);
			$message->addSource(new FileSource((string)$this->file->getFilename(), $node->getLine()));

			$this->catalogue->add($message);
		}
	}

	/**
	 * @param \PHPParser_Node $node
	 * @return string
	 */
	private function getTranslationId(PHPParser_Node $node) {
		return $this->getConcatenatedString($node);
	}

	/**
	 * @param \PHPParser_Node $node
	 * @return string
	 */
	private function getConcatenatedString(PHPParser_Node $node) {
		if ($node instanceof PHPParser_Node_Scalar_String) {
			return $node->value;
		}

		if ($node instanceof PHPParser_Node_Expr_Concat) {
			return $this->getConcatenatedString($node->left) . $this->getConcatenatedString($node->right);
		}

		throw new InvalidTranslationIdException(
			sprintf('Can only extract the translation ID from a scalar or concatenated string, but got "%s".'
			. ' Please refactor your code to make it extractable (in %s on line %d).',
			get_class($node), $this->file, $node->getLine())
		);
	}

	/**
	 * @param \PHPParser_Node $node
	 * @return boolean
	 */
	private function isFlashMessageAddMethodCall(PHPParser_Node $node) {
		$methodNames = array(
			'addSuccessFlash',
			'addErrorFlash',
			'addInfoFlash',
			'addSuccessFlashTwig',
			'addErrorFlashTwig',
			'addInfoFlashTwig',
		);

		if ($node instanceof PHPParser_Node_Expr_MethodCall && is_string($node->name)) {
			if (in_array($node->name, $methodNames)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $nodes
	 */
	public function beforeTraverse(array $nodes) {
		return null;
	}

	/**
	 * @param \PHPParser_Node $node
	 */
	public function leaveNode(PHPParser_Node $node) {
		return null;
	}

	/**
	 * @param array $nodes
	 */
	public function afterTraverse(array $nodes) {
		return null;
	}

	/**
	 *
	 * @param \SplFileInfo $file
	 * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
	 */
	public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue) {
		return null;
	}

	/**
	 * @param \SplFileInfo $file
	 * @param \JMS\TranslationBundle\Model\MessageCatalogue $catalogue
	 * @param \Twig_Node $ast
	 */
	public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $ast) {
		return null;
	}

}
