<?php

namespace Shopsys\ProductFeed\GoogleBundle;

use Shopsys\Plugin\PluginDataStorageProviderInterface;
use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Symfony\Component\Translation\TranslatorInterface;

class GoogleFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\Plugin\PluginDataStorageProviderInterface
     */
    private $pluginDataStorageProvider;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(
        PluginDataStorageProviderInterface $pluginDataStorageProvider,
        TranslatorInterface $translator
    ) {
        $this->pluginDataStorageProvider = $pluginDataStorageProvider;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Google';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'google';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysProductFeedGoogle/feed.xml.twig';
    }

    /**
     * @return string
     */
    public function getAdditionalInformation()
    {
        return $this->translator->trans(
            'Google Shopping product feed is not optimized for selling to Australia,
            Czechia, France, Germany, Italy, Netherlands, Spain, Switzerland, the UK,
            and the US. It is caused by missing \'shipping\' atribute.'
        );
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\StandardFeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        $productsDataById = $this->getProductsDataById($items);

        foreach ($items as $key => $item) {
            $productId = $item->getId();

            if (!$this->isProductShownOnDomain($productId, $productsDataById, $domainConfig)) {
                unset($items[$key]);
                continue;
            }
        }

        return $items;
    }

    /**
     * @param \Shopsys\ProductFeed\StandardFeedItemInterface[] $items
     * @return array
     */
    private function getProductsDataById(array $items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->getId();
        }

        $productDataStorage = $this->pluginDataStorageProvider
            ->getDataStorage(ShopsysProductFeedGoogleBundle::class, 'product');

        return $productDataStorage->getMultiple($productIds);
    }

    /**
     * @param int $productId
     * @param array $productsDataById
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return bool
     */
    protected function isProductShownOnDomain($productId, $productsDataById, DomainConfigInterface $domainConfig)
    {
        return $productsDataById[$productId]['show'][$domainConfig->getId()] ?? true;
    }
}
