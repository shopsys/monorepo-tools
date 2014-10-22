<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Article\ArticleRepository;
use SS6\ShopBundle\Model\Domain\Domain;
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
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Article\ArticleRepository $articleRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain
	 * @param \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function __construct(
		EntityManager $em,
		ArticleRepository $articleRepository,
		SelectedDomain $selectedDomain,
		Domain $domain
	) {
		$this->em = $em;
		$this->articleRepository = $articleRepository;
		$this->selectedDomain = $selectedDomain;
		$this->domain = $domain;
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function getById($articleId) {
		return $this->articleRepository->getById($articleId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getArticlesForMenuOnCurrentDomain() {
		return $this->articleRepository->getArticlesForMenu($this->domain->getId());
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
