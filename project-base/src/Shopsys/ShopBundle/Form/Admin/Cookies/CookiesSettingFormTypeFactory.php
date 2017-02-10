<?php

namespace Shopsys\ShopBundle\Form\Admin\Cookies;

use Shopsys\ShopBundle\Model\Article\ArticleFacade;

class CookiesSettingFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    public function __construct(ArticleFacade $articleFacade)
    {
        $this->articleFacade = $articleFacade;
    }

    public function createForDomain($domainId)
    {
        $articles = $this->articleFacade->getAllByDomainId($domainId);

        return new CookiesSettingFormType($articles);
    }
}
