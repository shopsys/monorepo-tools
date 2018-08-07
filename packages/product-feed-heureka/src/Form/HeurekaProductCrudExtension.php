<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainDataFactoryInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade;
use Symfony\Component\Translation\TranslatorInterface;

class HeurekaProductCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade
     */
    private $heurekaProductDomainFacade;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainDataFactoryInterface
     */
    private $heurekaProductDomainDataFactory;

    public function __construct(
        TranslatorInterface $translator,
        HeurekaProductDomainFacade $heurekaProductDomainFacade,
        HeurekaProductDomainDataFactoryInterface $heurekaProductDomainDataFactory
    ) {
        $this->translator = $translator;
        $this->heurekaProductDomainFacade = $heurekaProductDomainFacade;
        $this->heurekaProductDomainDataFactory = $heurekaProductDomainDataFactory;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return HeurekaProductFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Heureka.cz product feed');
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getData($productId)
    {
        $heurekaProductDomains = $this->heurekaProductDomainFacade->findByProductId($productId);

        $pluginData = [
            'cpc' => [],
        ];
        foreach ($heurekaProductDomains as $heurekaProductDomain) {
            $pluginData['cpc'][$heurekaProductDomain->getDomainId()] = $heurekaProductDomain->getCpc();
        }
        return $pluginData;
    }

    /**
     * @param int $productId
     * @param array $data
     */
    public function saveData($productId, $data)
    {
        $heurekaProductDomainsData = [];
        if (array_key_exists('cpc', $data)) {
            foreach ($data['cpc'] as $domainId => $cpc) {
                $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
                $heurekaProductDomainData->domainId = $domainId;
                $heurekaProductDomainData->cpc = $cpc;

                $heurekaProductDomainsData[] = $heurekaProductDomainData;
            }
        }
        $this->heurekaProductDomainFacade->saveHeurekaProductDomainsForProductId($productId, $heurekaProductDomainsData);
    }

    /**
     * @param int $productId
     */
    public function removeData($productId)
    {
        $this->heurekaProductDomainFacade->delete($productId);
    }
}
