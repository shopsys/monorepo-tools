<?php

namespace Shopsys\ShopBundle\Model\Script;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Script\ScriptData;

/**
 * @ORM\Table(name="scripts")
 * @ORM\Entity
 */
class Script
{

    const PLACEMENT_ORDER_SENT_PAGE = 'placementOrderSentPage';
    const PLACEMENT_ALL_PAGES = 'placementAllPages';
    const GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME = 'googleAnalyticsTrackingId';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $placement;

    /**
     * @param \Shopsys\ShopBundle\Model\Script\ScriptData $scriptData
     */
    public function __construct(ScriptData $scriptData) {
        $this->name = $scriptData->name;
        $this->code = $scriptData->code;
        $this->placement = $scriptData->placement;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPlacement() {
        return $this->placement;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Script\ScriptData $scriptData
     */
    public function edit(ScriptData $scriptData) {
        $this->name = $scriptData->name;
        $this->code = $scriptData->code;
        $this->placement = $scriptData->placement;
    }
}
