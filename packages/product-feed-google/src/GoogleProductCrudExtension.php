<?php

namespace Shopsys\ProductFeed\GoogleBundle;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade;
use Symfony\Component\Translation\TranslatorInterface;

class GoogleProductCrudExtension implements PluginCrudExtensionInterface
{

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade
     */
    private $googleProductDomainFacade;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade $googleProductDomainFacade
     */
    public function __construct(
        TranslatorInterface $translator,
        GoogleProductDomainFacade $googleProductDomainFacade
    ) {
        $this->translator = $translator;
        $this->googleProductDomainFacade = $googleProductDomainFacade;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return GoogleProductFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Google Shopping product feed');
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getData($productId)
    {
        $googleProductDomains = $this->googleProductDomainFacade->findByProductId($productId);

        $pluginData = [
            'show' => [],
        ];
        foreach ($googleProductDomains as $googleProductDomain) {
            $pluginData['show'][$googleProductDomain->getDomainId()] = $googleProductDomain->getShow();
        }
        return $pluginData;
    }

    /**
     * @param int $productId
     * @param array $data
     */
    public function saveData($productId, $data)
    {
        $googleProductDomainsDataIndexdByDomainId = [];
        foreach ($data as $productAttributeName => $productAttributeValuesByDomainIds) {
            foreach ($productAttributeValuesByDomainIds as $domainId => $productAttributeValue) {
                if (!array_key_exists($domainId, $googleProductDomainsDataIndexdByDomainId)) {
                    $googleProductDomainsDataIndexdByDomainId[$domainId] = new GoogleProductDomainData();

                    $googleProductDomainsDataIndexdByDomainId[$domainId]->domainId = $domainId;
                }

                $this->setGoogleProductDomainDataProperty(
                    $googleProductDomainsDataIndexdByDomainId[$domainId],
                    $productAttributeName,
                    $productAttributeValue
                );
            }
        }

        $this->googleProductDomainFacade->saveGoogleProductDomainsForProductId(
            $productId,
            $googleProductDomainsDataIndexdByDomainId
        );
    }

    /**
     * @param \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData $googleProductDomainData
     * @param string $propertyName
     * @param string $propertyValue
     */
    private function setGoogleProductDomainDataProperty(
        GoogleProductDomainData $googleProductDomainData,
        $propertyName,
        $propertyValue
    ) {
        switch ($propertyName) {
            case 'show':
                $googleProductDomainData->show = $propertyValue;
                break;
        }
    }

    /**
     * @param int $productId
     */
    public function removeData($productId)
    {
        $this->googleProductDomainFacade->delete($productId);
    }
}
