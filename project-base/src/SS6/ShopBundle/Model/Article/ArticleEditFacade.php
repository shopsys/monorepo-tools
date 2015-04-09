<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Article\ArticleRepository;
use SS6\ShopBundle\Model\Domain\Domain;

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
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Article\ArticleRepository $articleRepository
	 * @param \SS6\ShopBundle\Model\Domain\Domain
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	public function __construct(
		EntityManager $em,
		ArticleRepository $articleRepository,
		Domain $domain,
		FriendlyUrlFacade $friendlyUrlFacade
	) {
		$this->em = $em;
		$this->articleRepository = $articleRepository;
		$this->domain = $domain;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
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
		$article = new Article($articleData);

		$this->em->persist($article);
		$this->em->flush();
		$this->friendlyUrlFacade->createFriendlyUrlForDomain(
			'front_article_detail',
			$article->getId(),
			$article->getName(),
			$article->getDomainId()
		);
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
		$this->friendlyUrlFacade->createFriendlyUrlForDomain(
			'front_article_detail',
			$article->getId(),
			$article->getName(),
			$article->getDomainId()
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
