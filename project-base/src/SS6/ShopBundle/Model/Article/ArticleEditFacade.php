<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
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
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Article\ArticleRepository $articleRepository
	 * @param \SS6\ShopBundle\Component\Domain\Domain
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
	 * @return \SS6\ShopBundle\Model\Article\Article|null
	 */
	public function findById($articleId) {
		return $this->articleRepository->findById($articleId);
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function getById($articleId) {
		return $this->articleRepository->getById($articleId);
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function getVisibleById($articleId) {
		return $this->articleRepository->getVisibleById($articleId);
	}

	/**
	 * @param $domainId
	 * @return int
	 */
	public function getAllArticlesCountByDomainId($domainId) {
		return $this->articleRepository->getAllArticlesCountByDomainId($domainId);
	}

	/**
	 * @param string $placement
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getVisibleArticlesForPlacementOnCurrentDomain($placement) {
		return $this->articleRepository->getVisibleArticlesForPlacement($this->domain->getId(), $placement);
	}

	/**
	 * @param int $domainId
	 * @param string $placement
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement) {
		return $this->articleRepository->getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement);
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

		$this->friendlyUrlFacade->saveUrlListFormData('front_article_detail', $article->getId(), $articleData->urls);
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

	/**
	 * @param string[gridId][] $rowIdsByGridId
	 */
	public function saveOrdering(array $rowIdsByGridId) {
		foreach ($rowIdsByGridId as $gridId => $rowIds) {
			foreach ($rowIds as $position => $rowId) {
				$article = $this->articleRepository->findById($rowId);
				$article->setPosition($position);
				$article->setPlacement($gridId);
			}
		}
		$this->em->flush();
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getAllByDomainId($domainId) {
		return $this->articleRepository->getAllByDomainId($domainId);
	}

}
