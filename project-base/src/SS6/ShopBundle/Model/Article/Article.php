<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="articles")
 * @ORM\Entity
 */
class Article {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text;
	
	/**
	 * @param \SS6\ShopBundle\Model\Article\ArticleData $articleData
	 */
	public function __construct(ArticleData $articleData) {
		$this->name = $articleData->getName();
		$this->text = $articleData->getText();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Article\ArticleData $articleData
	 */
	public function edit(ArticleData $articleData) {
		$this->name = $articleData->getName();
		$this->text = $articleData->getText();
	}

	/**
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
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

}
