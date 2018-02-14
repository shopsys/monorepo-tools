<?php

namespace Shopsys\ShopBundle\Model\LegalConditions;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleFacade;

class LegalConditionsFacade
{
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
    public function findTermsAndConditions($domainId)
    {
        return $this->findArticle(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article|null $termsAndConditions
     * @param int $domainId
     */
    public function setTermsAndConditions(Article $termsAndConditions = null, $domainId)
    {
        $this->setArticle(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $termsAndConditions, $domainId);
    }

    /**
     * @return string
     */
    public function getTermsAndConditionsDownloadFilename()
    {
        return t('Terms-and-conditions.html');
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Article\Article|null
     */
    public function findPrivacyPolicy($domainId)
    {
        return $this->findArticle(Setting::PRIVACY_POLICY_ARTICLE_ID, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article|null $privacyPolicy
     * @param int $domainId
     */
    public function setPrivacyPolicy(Article $privacyPolicy = null, $domainId)
    {
        $this->setArticle(Setting::PRIVACY_POLICY_ARTICLE_ID, $privacyPolicy, $domainId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     * @return bool
     */
    public function isArticleUsedAsLegalConditions(Article $article)
    {
        foreach ($this->domain->getAllIds() as $domainId) {
            $legalConditionsArticles = [
                $this->findTermsAndConditions($domainId),
                $this->findPrivacyPolicy($domainId),
            ];

            if (in_array($article, $legalConditionsArticles, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $settingKey
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Article\Article|null
     */
    private function findArticle($settingKey, $domainId)
    {
        $articleId = $this->setting->getForDomain($settingKey, $domainId);

        if ($articleId !== null) {
            return $this->articleFacade->getById($articleId);
        }

        return null;
    }

    /**
     * @param string $settingKey
     * @param \Shopsys\ShopBundle\Model\Article\Article|null $privacyPolicy
     * @param int $domainId
     */
    private function setArticle($settingKey, Article $privacyPolicy = null, $domainId)
    {
        $articleId = null;
        if ($privacyPolicy !== null) {
            $articleId = $privacyPolicy->getId();
        }

        $this->setting->setForDomain($settingKey, $articleId, $domainId);
    }
}
