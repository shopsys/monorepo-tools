<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Article\ArticleRepository;

class ArticleEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleRepository
	 */
	private $articleRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Article\ArticleRepository $articleRepository
	 */
	public function __construct(EntityManager $em,
			ArticleRepository $articleRepository) {
		$this->em = $em;
		$this->articleRepository = $articleRepository;
	}

	/**
	 * @param string $name
	 * @param string|null $text
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function create($name, $text = null) {
		$article = new Article(
			$name,
			$text
		);

		$this->em->persist($article);
		$this->em->flush();

		return $article;
	}

	/**
	 * @param int $articleId
	 * @param string $name
	 * @param string|null $text
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function edit($articleId, $name, $text = null) {
		$article = $this->articleRepository->getById($articleId);

		$article->edit(
			$name,
			$text
		);

		$this->em->flush();

		return $article;
	}

	/**
	 * @param int $articleId
	 */
	public function delete($articleId) {
		$article = $this->articleRepository->getById($articleId);
		
		$this->em->remove($article);
		$this->em->flush();
	}

}
