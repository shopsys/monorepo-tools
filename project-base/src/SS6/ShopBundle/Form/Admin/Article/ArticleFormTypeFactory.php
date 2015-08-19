<?php

namespace SS6\ShopBundle\Form\Admin\Article;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticlePlacementList;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;

class ArticleFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticlePlacementList
	 */
	private $articlePlacementList;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		SeoSettingFacade $seoSettingFacade,
		ArticlePlacementList $articlePlacementList,
		Translator $translator
	) {
		$this->seoSettingFacade = $seoSettingFacade;
		$this->articlePlacementList = $articlePlacementList;
		$this->translator = $translator;
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
			$this->translator,
			$this->articlePlacementList->getTranslationsIndexedByValue(),
			$article,
			$this->seoSettingFacade->getDescriptionMainPage($domainId)
		);
	}

}
