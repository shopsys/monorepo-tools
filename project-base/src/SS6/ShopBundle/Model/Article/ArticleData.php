<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\Validator;
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
	 * @var int|null
	 */
	public $domainId;

	/**
	 * @param string|null $name
	 * @param string|null $text
	 * @param int|null $domainId
	 */
	public function __construct($name = null, $text = null, $domainId = null) {
		$this->name = $name;
		$this->text = $text;
		$this->domainId = $domainId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 */
	public function setFromEntity(Article $article) {
		$this->name = $article->getName();
		$this->text = $article->getText();
		$this->domainId = $article->getDomainId();
	}
}
