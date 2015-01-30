<?php

namespace SS6\ShopBundle\Component\Javascript\Parser\Translator;

use SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser;
use SS6\ShopBundle\Component\Translation\TransMethodSpecification;

class JsTranslatorCallParserFactory {

	/**
	 * @return \SS6\ShopBundle\Component\Javascript\Parser\Translator\JsTranslatorCallParser
	 */
	public function create() {
		$transMethodSpecifications = [
			new TransMethodSpecification('SS6.translator.trans', 0, 2),
			new TransMethodSpecification('SS6.translator.transChoice', 0, 3),
		];

		return new JsTranslatorCallParser($transMethodSpecifications);
	}

}
