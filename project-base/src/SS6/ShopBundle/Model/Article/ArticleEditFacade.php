<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Article\ArticleRepository;
use SS6\ShopBundle\Model\Domain\SelectedDomain;

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
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Article\ArticleRepository $articleRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	public function __construct(
		EntityManager $em,
		ArticleRepository $articleRepository,
		SelectedDomain $selectedDomain
	) {
		$this->em = $em;
		$this->articleRepository = $articleRepository;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\ArticleData $articleData
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function create(ArticleData $articleData) {
		$article = new Article($articleData, $this->selectedDomain->getId());

		$this->em->persist($article);
		$this->em->flush();

		return $article;
	}

	/**
	 * @param int $articleId
	 * @param \SS6\ShopBundle\Model\Article\ArticleData $articleData
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function edit($articleId, ArticleData $articleData) {
		$article = $this->articleRepository->getById($articleId);
		$article->edit($articleData);

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
