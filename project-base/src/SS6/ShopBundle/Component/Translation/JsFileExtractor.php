<?php

namespace SS6\ShopBundle\Component\Translation;

import('PLUG.JavaScript.JParser');
import('PLUG.JavaScript.JTokenizer');
import('PLUG.parsing.ParseError');

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;
use JParser;
use JTokenizer;
use SplFileInfo;
use SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\JsTranslatorCallParser;
use Twig_Node;

class JsFileExtractor implements FileVisitorInterface {

	/**
	 * @var \SplFileInfo
	 */
	private $file;

	/**
	 * @var \JMS\TranslationBundle\Model\MessageCatalogue
	 */
	private $catalogue;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\JsTranslatorCallParser
	 */
	private $jsTranslatorCallParser;

	public function __construct(JsTranslatorCallParser $jsTranslatorCallParser) {
		$this->jsTranslatorCallParser = $jsTranslatorCallParser;
	}

	public function visitFile(SplFileInfo $file, MessageCatalogue $catalogue) {
		if ('.js' !== substr($file, -3)) {
			return;
		}

		$this->file = $file;
		$this->catalogue = $catalogue;

		$filename = $file->getRealPath();
		$contents = file_get_contents($filename);

		try {
			$this->parseFile($contents);
		} catch (\ParseError $ex) {
			throw new \SS6\ShopBundle\Component\Translation\Exception\ExtractionException(
				$ex->getMessage() . "\n" . 'in file ' . $this->file->getRealPath()
			);
		} catch (\SS6\ShopBundle\Component\Translation\JsTranslatorCallParser\Exception\ParseException $ex) {
			throw new \SS6\ShopBundle\Component\Translation\Exception\ExtractionException(
				$ex->getMessage() . ' in file ' . $this->file->getRealPath()
			);
		}
	}

	public function visitPhpFile(SplFileInfo $file, MessageCatalogue $catalogue, array $ast) {

	}

	public function visitTwigFile(SplFileInfo $file, MessageCatalogue $catalogue, Twig_Node $node) {

	}

	private function parseFile($contents) {
		$node = JParser::parse_string($contents, true, JParser::class, JTokenizer::class);

		$jsTranslatorCalls = $this->jsTranslatorCallParser->parse($node);

		foreach ($jsTranslatorCalls as $jsTranslatorCall) {
			$message = new Message($jsTranslatorCall->getMessageId(), $jsTranslatorCall->getDomain());
			$message->addSource(new FileSource(
				$this->file->getFilename(),
				$jsTranslatorCall->getCallExprNode()->get_line_num(),
				$jsTranslatorCall->getCallExprNode()->get_col_num()
			));

			$this->catalogue->add($message);
		}
	}

}
