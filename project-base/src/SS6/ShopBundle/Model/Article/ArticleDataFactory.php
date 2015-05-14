<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Form\UrlListType;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;

class ArticleDataFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
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

		foreach ($this->domain->getAll() as $domainCongig) {
			$articleData->urls[UrlListType::MAIN_ON_DOMAINS][$domainCongig->getId()] =
				$this->friendlyUrlFacade->findMainFriendlyUrl(
					$domainCongig->getId(),
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
