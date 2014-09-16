<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Model\Article\Article;

class ArticleData {
	
	/**
	 * @var string|null
	 */
	private $name;
	
	/**
	 * @var string|null
	 */
	private $text;

	/**
	 * @param string|null $name
	 * @param string|null $text
	 */
	public function __construct($name = null, $text = null) {
		$this->name = $name;
		$this->text = $text;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @param string|null $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string|null $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 */
	public function setFromEntity(Article $article) {
		$this->setName($article->getName());
		$this->setText($article->getText());
	}
}
