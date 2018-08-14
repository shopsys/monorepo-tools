<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Twig_SimpleFunction;

class SeoExtension extends \Twig_Extension
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        SeoSettingFacade $seoSettingFacade,
        Domain $domain
    ) {
        $this->seoSettingFacade = $seoSettingFacade;
        $this->domain = $domain;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getSeoTitleAddOn', [$this, 'getSeoTitleAddOn']),
            new Twig_SimpleFunction('getSeoMetaDescription', [$this, 'getSeoMetaDescription']),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'seo';
    }

    /**
     * @return string
     */
    public function getSeoTitleAddOn()
    {
        $currentDomainId = $this->domain->getId();
        return $this->seoSettingFacade->getTitleAddOn($currentDomainId);
    }

    /**
     * @return string
     */
    public function getSeoMetaDescription()
    {
        $currentDomainId = $this->domain->getId();
        return $this->seoSettingFacade->getDescriptionMainPage($currentDomainId);
    }
}
