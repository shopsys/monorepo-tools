<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Translator;

use SS6\ShopBundle\Component\Javascript\Parser\JsStringParser;
use SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use SS6\ShopBundle\Component\Translation\TransMethodSpecification;

class JsTranslatorCallParserFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Javascript\Parser\JsStringParser
	 */
	private $jsStringParser;

	/**
	 * @param \SS6\ShopBundle\Component\Javascript\Parser\JsStringParser $jsStringParser
	 */
	public function __construct(JsStringParser $jsStringParser) {
		$this->jsStringParser = $jsStringParser;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
	 */
	public function create() {
		$transMethodSpecifications = [
			new TransMethodSpecification('SS6.translator.trans', 0, 2),
			new TransMethodSpecification('SS6.translator.transChoice', 0, 3),
		];

		return new JsTranslatorCallParser(
			$this->jsStringParser,
			$transMethodSpecifications
		);
	}

}
