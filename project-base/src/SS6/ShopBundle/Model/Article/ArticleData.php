<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Form\UrlListType;
use SS6\ShopBundle\Model\Article\Article;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Article\Article")
 */
class ArticleData {

	/**
	 * @var string|null
	 */
	public $name;

	/**
	 * @var string|null
	 */
	public $text;

	/**
	 * @var string|null
	 */
	public $seoTitle;

	/**
	 * @var string|null
	 */
	public $seoMetaDescription;

	/**
	 * @var int|null
	 */
	public $domainId;

	/**
	 * @var array
	 */
	public $urls;

	/**
	 * @var string
	 */
	public $placement;

	public function __construct() {
		$this->urls = [
			UrlListType::TO_DELETE => [],
			UrlListType::MAIN_ON_DOMAINS => [],
			UrlListType::NEW_URLS => [],
		];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 */
	public function setFromEntity(Article $article) {
		$this->name = $article->getName();
		$this->text = $article->getText();
		$this->seoTitle = $article->getSeoTitle();
		$this->seoMetaDescription = $article->getSeoMetaDescription();
		$this->domainId = $article->getDomainId();
		$this->placement = $article->getPlacement();
	}
}
