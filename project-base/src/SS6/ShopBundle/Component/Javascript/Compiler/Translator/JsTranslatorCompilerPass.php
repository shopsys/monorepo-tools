<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler\Translator;

import('PLUG.JavaScript.JNodes.nonterminal.JProgramNode');

use JProgramNode;
use SS6\ShopBundle\Component\Javascript\Compiler\JsCompilerPassInterface;
use SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use SS6\ShopBundle\Component\Translation\Translator;

class JsTranslatorCompilerPass implements JsCompilerPassInterface {

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
		Translator $translator
	) {
		$this->jsTranslatorCallParser = $jsTranslatorCallParser;
		$this->translator = $translator;
	}

	/**
	 * @param \JProgramNode $node
	 */
	public function process(JProgramNode $node) {
		$jsTranslatorsCalls = $this->jsTranslatorCallParser->parse($node);

		foreach ($jsTranslatorsCalls as $jsTranslatorsCall) {
			$messageIdArgumentNode = $jsTranslatorsCall->getMessageIdArgumentNode();

			$translatedMessage = $this->translator->trans($jsTranslatorsCall->getMessageId());

			$messageIdArgumentNode->terminate(json_encode($translatedMessage));
		}
	}

}
