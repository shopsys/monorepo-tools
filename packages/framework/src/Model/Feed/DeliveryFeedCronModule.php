<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class DeliveryFeedCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedFacade
     */
    private $feedFacade;

    public function __construct(FeedFacade $feedFacade)
    {
        $this->feedFacade = $feedFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->feedFacade->generateDeliveryFeeds();
    }
}
