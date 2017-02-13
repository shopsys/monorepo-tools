<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;

class FeedConfig
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $templateFilepath;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
     */
    private $feedItemRepository;

    /**
     * @param string $label
     * @param string $name
     * @param string $templateFilepath
     * @param \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface $feedItemRepository
     */
    public function __construct(
        $label,
        $name,
        $templateFilepath,
        FeedItemRepositoryInterface $feedItemRepository
    ) {
        $this->label = $label;
        $this->name = $name;
        $this->templateFilepath = $templateFilepath;
        $this->feedItemRepository = $feedItemRepository;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return $this->name;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string $feedHash
     *
     * @return string
     */
    public function getFeedFilename(DomainConfig $domainConfig, $feedHash)
    {
        return $feedHash . '_' . $this->name . '_' . $domainConfig->getId() . '.xml';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return $this->templateFilepath;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
