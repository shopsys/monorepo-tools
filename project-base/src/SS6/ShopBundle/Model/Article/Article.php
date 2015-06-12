<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="articles")
 * @ORM\Entity
 */
class Article {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

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
		$this->domainId = $articleData->domainId;
		$this->name = $articleData->name;
		$this->text = $articleData->text;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\ArticleData $articleData
	 */
	public function edit(ArticleData $articleData) {
		$this->name = $articleData->name;
		$this->text = $articleData->text;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
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
