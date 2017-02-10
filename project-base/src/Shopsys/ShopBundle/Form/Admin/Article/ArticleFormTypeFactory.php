<?php

namespace Shopsys\ShopBundle\Form\Admin\Article;

use Shopsys\ShopBundle\Form\Admin\Article\ArticleFormType;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticlePlacementList;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;

class ArticleFormTypeFactory {

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticlePlacementList
     */
    private $articlePlacementList;

    public function __construct(
        SeoSettingFacade $seoSettingFacade,
        ArticlePlacementList $articlePlacementList
    ) {
        $this->seoSettingFacade = $seoSettingFacade;
        $this->articlePlacementList = $articlePlacementList;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     * @return \Shopsys\ShopBundle\Form\Admin\Article\ArticleFormType
     */
    public function create(
        $domainId,
        Article $article = null
    ) {
        return new ArticleFormType(
            $this->articlePlacementList->getTranslationsIndexedByValue(),
            $article,
            $this->seoSettingFacade->getDescriptionMainPage($domainId)
        );
    }

}
