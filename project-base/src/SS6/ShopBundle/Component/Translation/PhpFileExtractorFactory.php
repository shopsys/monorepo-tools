<?php

namespace SS6\ShopBundle\Component\Translation;

use Doctrine\Common\Annotations\DocParser;
use SS6\ShopBundle\Component\Translation\PhpFileExtractor;
use SS6\ShopBundle\Component\Translation\TransMethodSpecification;

class PhpFileExtractorFactory {

	/**
	 * @var \Doctrine\Common\Annotations\DocParser
	 */
	private $docParser;

	public function __construct(DocParser $docParser) {
		$this->docParser = $docParser;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Translation\PhpFileExtractor
	 */
	public function create() {
		$transMethodSpecifications = [
			new TransMethodSpecification('trans', 0, 2),
			new TransMethodSpecification('transChoice', 0, 3),

			new TransMethodSpecification('addSuccessFlash'),
			new TransMethodSpecification('addErrorFlash'),
			new TransMethodSpecification('addInfoFlash'),
			new TransMethodSpecification('addSuccessFlashTwig'),
			new TransMethodSpecification('addErrorFlashTwig'),
			new TransMethodSpecification('addInfoFlashTwig'),
		];

		return new PhpFileExtractor($this->docParser, $transMethodSpecifications);
	}

}
