<?php

namespace SS6\ShopBundle\Model\TermsAndConditions;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class TermsAndConditionsFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		ArticleEditFacade $articleEditFacade,
		Setting $setting,
		Domain $domain
	) {
		$this->articleEditFacade = $articleEditFacade;
		$this->setting = $setting;
		$this->domain = $domain;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Article\Article|null
	 */
	public function findTermsAndConditionsArticleByDomainId($domainId) {
		$termsAndConditionsArticleId = $this->setting->getForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId);

		if ($termsAndConditionsArticleId !== null) {
			return $this->articleEditFacade->findById(
				$this->setting->getForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId)
			);
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article|null $termsAndConditionsArticle
	 * @param int $domainId
	 */
	public function setTermsAndConditionsArticleOnDomain($termsAndConditionsArticle, $domainId) {
		$termsAndConditionsArticleId = null;
		if ($termsAndConditionsArticle !== null) {
			$termsAndConditionsArticleId = $termsAndConditionsArticle->getId();
		}
		$this->setting->setForDomain(
			Setting::TERMS_AND_CONDITIONS_ARTICLE_ID,
			$termsAndConditionsArticleId,
			$domainId
		);
	}

	/**
	 * @return string
	 */
	public function getDownloadFilename() {
		return t('Terms-and-conditions.html');
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 * @return bool
	 */
	public function isArticleUsedAsTermsAndConditions(Article $article) {
		foreach ($this->domain->getAll() as $domainConfig) {
			if ($this->findTermsAndConditionsArticleByDomainId($domainConfig->getId()) === $article) {
				return true;
			}
		}

		return false;
	}

}
