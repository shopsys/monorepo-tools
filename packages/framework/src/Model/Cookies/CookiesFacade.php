<?php

namespace Shopsys\FrameworkBundle\Model\Cookies;

use Shopsys\Environment;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;
use Symfony\Component\HttpFoundation\RequestStack;

class CookiesFacade
{
    const EU_COOKIES_COOKIE_CONSENT_NAME = 'eu-cookies';

    /**
     * @var string
     */
    private $environment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param string $environment
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleFacade $articleFacade
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        $environment,
        ArticleFacade $articleFacade,
        Setting $setting,
        Domain $domain,
        RequestStack $requestStack
    ) {
        $this->environment = $environment;
        $this->articleFacade = $articleFacade;
        $this->setting = $setting;
        $this->domain = $domain;
        $this->requestStack = $requestStack;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Article\Article|null
     */
    public function findCookiesArticleByDomainId($domainId)
    {
        $cookiesArticleId = $this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId);

        if ($cookiesArticleId !== null) {
            return $this->articleFacade->findById(
                $this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId)
            );
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article|null $cookiesArticle
     * @param int $domainId
     */
    public function setCookiesArticleOnDomain($cookiesArticle, $domainId)
    {
        $cookiesArticleId = null;
        if ($cookiesArticle !== null) {
            $cookiesArticleId = $cookiesArticle->getId();
        }
        $this->setting->setForDomain(
            Setting::COOKIES_ARTICLE_ID,
            $cookiesArticleId,
            $domainId
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article $article
     * @return bool
     */
    public function isArticleUsedAsCookiesInfo(Article $article)
    {
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
    public function isCookiesConsentGiven()
    {
        // Cookie fixed bar overlays some elements in viewport and mouseover fails on these elements in acceptance tests.
        if ($this->environment === Environment::ENVIRONMENT_TEST) {
            return true;
        }
        $masterRequest = $this->requestStack->getMasterRequest();

        return $masterRequest->cookies->has(self::EU_COOKIES_COOKIE_CONSENT_NAME);
    }
}
