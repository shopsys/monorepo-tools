<?php

namespace Shopsys\ShopBundle\Model\Heureka;

use Heureka\ShopCertification;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Heureka\HeurekaSetting;
use Shopsys\ShopBundle\Model\Heureka\HeurekaShopCertificationService;
use Shopsys\ShopBundle\Model\Order\Order;

class HeurekaShopCertificationFactory
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting
     */
    private $heurekaSetting;

    /**
     * @var \Shopsys\ShopBundle\Model\Heureka\HeurekaShopCertificationService
     */
    private $heurekaShopCertificationService;

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Shopsys\ShopBundle\Model\Heureka\HeurekaSetting
     * @param \Shopsys\ShopBundle\Model\Heureka\HeurekaShopCertificationService
     */
    public function __construct(
        Domain $domain,
        HeurekaSetting $heurekaSetting,
        HeurekaShopCertificationService $heurekaShopCertificationService
    ) {
        $this->domain = $domain;
        $this->heurekaSetting = $heurekaSetting;
        $this->heurekaShopCertificationService = $heurekaShopCertificationService;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @return \Heureka\ShopCertification
     */
    public function create(Order $order) {
        $domainConfig = $this->domain->getDomainConfigById($order->getDomainId());

        $languageId = $this->heurekaShopCertificationService->getLanguageIdByLocale($domainConfig->getLocale());
        $heurekaApiKey = $this->heurekaSetting->getApiKeyByDomainId($domainConfig->getId());
        $options = ['service' => $languageId];

        $heurekaShopCertification = new ShopCertification($heurekaApiKey, $options);
        $heurekaShopCertification->setOrderId($order->getId());
        $heurekaShopCertification->setEmail($order->getEmail());
        foreach ($order->getProductItems() as $item) {
            if ($item->hasProduct()) {
                $heurekaShopCertification->addProductItemId($item->getProduct()->getId());
            }
        }

        return $heurekaShopCertification;
    }
}
