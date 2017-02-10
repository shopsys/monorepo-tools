<?php

namespace Shopsys\ShopBundle\Form\Admin\TermsAndConditions;

use Shopsys\ShopBundle\Model\Article\ArticleFacade;

class TermsAndConditionsSettingFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    public function __construct(ArticleFacade $articleFacade) {
        $this->articleFacade = $articleFacade;
    }

    public function createForDomain($domainId) {
        $articles = $this->articleFacade->getAllByDomainId($domainId);

        return new TermsAndConditionsSettingFormType($articles);
    }
}
