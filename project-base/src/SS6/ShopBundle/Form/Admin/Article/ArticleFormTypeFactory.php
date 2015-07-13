<?php

namespace SS6\ShopBundle\Form\Admin\Article;

use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;

class ArticleFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	public function __construct(
		SeoSettingFacade $seoSettingFacade
	) {
		$this->seoSettingFacade = $seoSettingFacade;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 * @return \SS6\ShopBundle\Form\Admin\Article\ArticleFormType
	 */
	public function create(
		$domainId,
		Article $article = null
	) {
		return new ArticleFormType(
			$article,
			$this->seoSettingFacade->getDescriptionMainPage($domainId)
		);
	}

}
