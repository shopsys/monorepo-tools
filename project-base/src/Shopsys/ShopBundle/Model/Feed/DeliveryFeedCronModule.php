<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Cron\CronModuleInterface;
use Shopsys\ShopBundle\Model\Feed\FeedFacade;
use Symfony\Bridge\Monolog\Logger;

class DeliveryFeedCronModule implements CronModuleInterface {

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedFacade
     */
    private $feedFacade;

    public function __construct(FeedFacade $feedFacade) {
        $this->feedFacade = $feedFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger) {

    }

    public function run() {
        $this->feedFacade->generateDeliveryFeeds();
    }

}
