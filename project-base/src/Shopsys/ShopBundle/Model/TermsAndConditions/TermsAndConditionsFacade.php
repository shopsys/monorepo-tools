<?php

namespace Shopsys\ShopBundle\Model\TermsAndConditions;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleFacade;

class TermsAndConditionsFacade {

	/**
	 * @var \Shopsys\ShopBundle\Model\Article\ArticleFacade
	 */
	private $articleFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		ArticleFacade $articleFacade,
		Setting $setting,
		Domain $domain
	) {
		$this->articleFacade = $articleFacade;
		$this->setting = $setting;
		$this->domain = $domain;
	}

	/**
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Article\Article|null
	 */
	public function findTermsAndConditionsArticleByDomainId($domainId) {
		$termsAndConditionsArticleId = $this->setting->getForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId);

		if ($termsAndConditionsArticleId !== null) {
			return $this->articleFacade->findById(
				$this->setting->getForDomain(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId)
			);
		}

		return null;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Article\Article|null $termsAndConditionsArticle
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
	 * @param \Shopsys\ShopBundle\Model\Article\Article $article
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
