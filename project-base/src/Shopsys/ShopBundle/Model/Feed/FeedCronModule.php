<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Feed\FeedFacade;
use Symfony\Bridge\Monolog\Logger;

class FeedCronModule implements IteratedCronModuleInterface
{

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedFacade
     */
    private $feedFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig|null
     */
    private $feedGenerationConfigToContinue;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(FeedFacade $feedFacade, Setting $setting) {
        $this->feedFacade = $feedFacade;
        $this->setting = $setting;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger) {

    }

    /**
     * @inheritdoc
     */
    public function iterate() {
        if ($this->feedGenerationConfigToContinue === null) {
            $this->feedGenerationConfigToContinue = $this->feedFacade->getFirstFeedGenerationConfig();
        }
        $this->feedGenerationConfigToContinue = $this->feedFacade->generateFeedsIteratively($this->feedGenerationConfigToContinue);

        return $this->feedGenerationConfigToContinue !== null;
    }

    /**
     * @inheritdoc
     */
    public function sleep() {
        $this->setting->set(
            Setting::FEED_NAME_TO_CONTINUE,
            $this->feedGenerationConfigToContinue->getFeedName()
        );
        $this->setting->set(
            Setting::FEED_DOMAIN_ID_TO_CONTINUE,
            $this->feedGenerationConfigToContinue->getDomainId()
        );
        $this->setting->set(
            Setting::FEED_ITEM_ID_TO_CONTINUE,
            $this->feedGenerationConfigToContinue->getFeedItemId()
        );
    }

    /**
     * @inheritdoc
     */
    public function wakeUp() {
        $this->feedGenerationConfigToContinue = new FeedGenerationConfig(
            $this->setting->get(Setting::FEED_NAME_TO_CONTINUE),
            $this->setting->get(Setting::FEED_DOMAIN_ID_TO_CONTINUE),
            $this->setting->get(Setting::FEED_ITEM_ID_TO_CONTINUE)
        );
    }

}
