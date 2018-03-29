<?php

namespace Shopsys\ProductFeed\GoogleBundle;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade;
use Symfony\Component\Translation\TranslatorInterface;

class GoogleFeedConfig implements FeedConfigInterface
{

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainFacade
     */
    private $googleProductDomainFacade;

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
        $productsIds = [];
        foreach ($items as $item) {
            $productsIds[] = $item->getId();
        }

        $googleProductDomainsIndexedByProductId = $this->googleProductDomainFacade->getGoogleProductDomainsByProductsIdsDomainIdIndexedByProductId(
            $productsIds,
            $domainConfig->getId()
        );

        foreach ($items as $key => $item) {
            $show = isset($googleProductDomainsIndexedByProductId[$item->getId()]) ?
                $googleProductDomainsIndexedByProductId[$item->getId()]->getShow() : true;

            if (!$show) {
                unset($items[$key]);
                continue;
            }
        }

        return $items;
    }
}
