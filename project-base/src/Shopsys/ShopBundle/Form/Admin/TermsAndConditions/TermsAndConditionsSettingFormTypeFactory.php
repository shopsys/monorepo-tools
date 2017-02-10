<?php

namespace Shopsys\ShopBundle\Form\Admin\TermsAndConditions;

use Shopsys\ShopBundle\Model\Article\ArticleEditFacade;

class TermsAndConditionsSettingFormTypeFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Article\ArticleEditFacade
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
