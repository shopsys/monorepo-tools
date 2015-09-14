<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;

class ArticleRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getArticleRepository() {
		return $this->em->getRepository(Article::class);
	}

	/**
	 * @param string $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function findById($articleId) {
		return $this->getArticleRepository()->find($articleId);
	}

	/**
	 * @param int $domainId
	 * @param string $placement
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement) {
		return $this->getArticlesByDomainIdQueryBuilder($domainId)
			->andWhere('a.placement = :placement')->setParameter('placement', $placement)
			->orderBy('a.position, a.id');
	}

	/**
	 * @param int $domainId
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getArticlesByDomainIdQueryBuilder($domainId) {
		return $this->em->createQueryBuilder()
			->select('a')
			->from(Article::class, 'a')
			->where('a.domainId = :domainId')->setParameter('domainId', $domainId);
	}

	/**
	 * @param int $domainId
	 * @param string $placement
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getArticlesForPlacement($domainId, $placement) {
		$queryBuilder = $this->getOrderedArticlesByDomainIdAndPlacementQueryBuilder(
			$domainId,
			$placement
		);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function getById($articleId) {
		$user = $this->getArticleRepository()->find($articleId);
		if ($user === null) {
			$message = 'Article with ID ' . $articleId . ' not found';
			throw new \SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException($message);
		}
		return $user;
	}

}
