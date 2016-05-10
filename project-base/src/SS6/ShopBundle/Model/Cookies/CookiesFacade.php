<?php

namespace SS6\ShopBundle\Model\Cookies;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;
use Symfony\Component\HttpFoundation\RequestStack;

class CookiesFacade {

	const EU_COOKIES_COOKIE_CONSENT_NAME = 'eu-cookies';

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

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

	public function __construct(
		ArticleEditFacade $articleEditFacade,
		Setting $setting,
		Domain $domain,
		RequestStack $requestStack
	) {
		$this->articleEditFacade = $articleEditFacade;
		$this->setting = $setting;
		$this->domain = $domain;
		$this->requestStack = $requestStack;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Article\Article|null
	 */
	public function findCookiesArticleByDomainId($domainId) {
		$cookiesArticleId = $this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId);

		if ($cookiesArticleId !== null) {
			return $this->articleEditFacade->findById(
				$this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId)
			);
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article|null $cookiesArticle
	 * @param int $domainId
	 */
	public function setCookiesArticleOnDomain($cookiesArticle, $domainId) {
		$cookiesArticleId = null;
		if ($cookiesArticle !== null) {
			$cookiesArticleId = $cookiesArticle->getId();
		}
		$this->setting->set(
			Setting::COOKIES_ARTICLE_ID,
			$cookiesArticleId,
			$domainId
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article $article
	 * @return bool
	 */
	public function isArticleUsedAsCookiesInfo(Article $article) {
		foreach ($this->domain->getAll() as $domainConfig) {
			if ($this->findCookiesArticleByDomainId($domainConfig->getId()) === $article) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isCookiesConsentGiven() {
		$masterRequest = $this->requestStack->getMasterRequest();

		return $masterRequest->cookies->has(self::EU_COOKIES_COOKIE_CONSENT_NAME);
	}
}
