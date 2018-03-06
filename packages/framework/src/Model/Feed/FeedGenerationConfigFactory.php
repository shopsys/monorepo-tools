<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Domain;

class FeedGenerationConfigFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedConfigFacade
     */
    private $feedConfigFacade;

    public function __construct(Domain $domain, FeedConfigFacade $feedConfigFacade)
    {
        $this->domain = $domain;
        $this->feedConfigFacade = $feedConfigFacade;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedGenerationConfig[]
     */
    public function createAllForStandardFeeds()
    {
        $feedGenerationConfigs = [];
        foreach ($this->feedConfigFacade->getStandardFeedConfigs() as $feedConfig) {
            foreach ($this->domain->getAll() as $domainConfig) {
                $feedGenerationConfigs[] = new FeedGenerationConfig(
                    $feedConfig->getFeedName(),
                    $domainConfig->getId()
                );
            }
        }

        return $feedGenerationConfigs;
    }
}
