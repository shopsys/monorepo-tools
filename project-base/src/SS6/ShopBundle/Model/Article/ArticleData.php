<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Form\UrlListData;
use SS6\ShopBundle\Model\Article\Article;

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
	 * @var \SS6\ShopBundle\Form\UrlListData
	 */
	public $urls;

	/**
	 * @var string
	 */
	public $placement;

	/**
	 * @var bool|null
	 */
	public $hidden;

	public function __construct() {
		$this->urls = new UrlListData();
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
		$this->hidden = $article->isHidden();
	}
}
