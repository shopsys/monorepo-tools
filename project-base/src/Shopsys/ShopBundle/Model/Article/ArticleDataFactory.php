<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class ArticleDataFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		FriendlyUrlFacade $friendlyUrlFacade,
		Domain $domain,
		SelectedDomain $selectedDomain
	) {
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 * @return \SS6\ShopBundle\Model\Article\ArticleData
	 */
	public function createFromArticle(Article $article) {
		$articleData = new ArticleData();
		$articleData->setFromEntity($article);

		foreach ($this->domain->getAll() as $domainConfig) {
			$articleData->urls->mainOnDomains[$domainConfig->getId()] =
				$this->friendlyUrlFacade->findMainFriendlyUrl(
					$domainConfig->getId(),
					'front_article_detail',
					$article->getId()
				);
		}

		return $articleData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Article\ArticleData
	 */
	public function createDefault() {
		$articleData = new ArticleData();
		$articleData->domainId = $this->selectedDomain->getId();

		return $articleData;
	}

}
