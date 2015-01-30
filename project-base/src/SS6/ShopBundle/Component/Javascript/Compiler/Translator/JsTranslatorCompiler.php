<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler\Translator;

import('PLUG.JavaScript.JParser');
import('PLUG.JavaScript.JTokenizer');
import('PLUG.parsing.ParseError');

use JParser;
use JTokenizer;
use SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use Symfony\Component\Translation\TranslatorInterface;

class JsTranslatorCompiler {

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
	 */
	private $jsTranslatorCallParser;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		JsTranslatorCallParser $jsTranslatorCallParser,
		TranslatorInterface $translator
	) {
		$this->jsTranslatorCallParser = $jsTranslatorCallParser;
		$this->translator = $translator;
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public function translate($content) {
		$node = JParser::parse_string($content, true, JParser::class, JTokenizer::class);

		$jsTranslatorsCalls = $this->jsTranslatorCallParser->parse($node);

		foreach ($jsTranslatorsCalls as $jsTranslatorsCall) {
			$callExprNode = $jsTranslatorsCall->getCallExprNode();
			$messageIdArgumentNode = $jsTranslatorsCall->getMessageIdArgumentNode();

			$translatedMessage = $this->translator->trans($jsTranslatorsCall->getMessageId());

			$messageIdArgumentNode->terminate(json_encode($translatedMessage));
		}

		return $node->format();
	}

}
