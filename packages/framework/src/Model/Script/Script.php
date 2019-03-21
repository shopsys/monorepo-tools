<?php

namespace Shopsys\FrameworkBundle\Model\Script;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="scripts")
 * @ORM\Entity
 */
class Script
{
    public const PLACEMENT_ORDER_SENT_PAGE = 'placementOrderSentPage';
    public const PLACEMENT_ALL_PAGES = 'placementAllPages';
    public const GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME = 'googleAnalyticsTrackingId';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $placement;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $scriptData
     */
    public function __construct(ScriptData $scriptData)
    {
        $this->name = $scriptData->name;
        $this->code = $scriptData->code;
        $this->placement = $scriptData->placement;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $scriptData
     */
    public function edit(ScriptData $scriptData)
    {
        $this->name = $scriptData->name;
        $this->code = $scriptData->code;
        $this->placement = $scriptData->placement;
    }
}
