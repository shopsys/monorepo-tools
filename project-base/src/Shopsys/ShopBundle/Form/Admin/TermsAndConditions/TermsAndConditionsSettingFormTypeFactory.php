<?php

namespace SS6\ShopBundle\Form\Admin\TermsAndConditions;

use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class TermsAndConditionsSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	public function __construct(ArticleEditFacade $articleEditFacade) {
		$this->articleEditFacade = $articleEditFacade;
	}

	public function createForDomain($domainId) {
		$articles = $this->articleEditFacade->getAllByDomainId($domainId);

		return new TermsAndConditionsSettingFormType($articles);
	}
}
