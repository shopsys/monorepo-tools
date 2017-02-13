<?php

namespace Shopsys\ShopBundle\Form\Admin\ShopInfo;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade;

class ShopInfoSettingFormTypeFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Model\ShopInfo\ShopInfoSettingFacade
     */
    private $shopInfoSettingFacade;

    public function __construct(
        Domain $domain,
        SelectedDomain $selectedDomain,
        ShopInfoSettingFacade $shopInfoSettingFacade
    ) {
        $this->domain = $domain;
        $this->selectedDomain = $selectedDomain;
        $this->shopInfoSettingFacade = $shopInfoSettingFacade;
    }

    /**
     * @return \Shopsys\ShopBundle\Form\Admin\ShopInfo\ShopInfoSettingFormType
     */
    public function create()
    {
        return new ShopInfoSettingFormType();
    }
}
