<?php

namespace Shopsys\ShopBundle\Form\Admin\Cookies;

use Shopsys\ShopBundle\Model\Article\ArticleEditFacade;

class CookiesSettingFormTypeFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Article\ArticleEditFacade
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
