<?php

namespace SS6\ShopBundle\Form\Admin\Cookies;

use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class CookiesSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	public function __construct(ArticleEditFacade $articleEditFacade) {
		$this->articleEditFacade = $articleEditFacade;
	}

	public function createForDomain($domainId) {
		$articles = $this->articleEditFacade->getAllByDomainId($domainId);

		return new CookiesSettingFormType($articles);
	}
}
