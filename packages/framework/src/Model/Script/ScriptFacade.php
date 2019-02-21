<?php

namespace Shopsys\FrameworkBundle\Model\Script;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Order\Order;

class ScriptFacade
{
    const VARIABLE_NUMBER = '{number}';
    const VARIABLE_TOTAL_PRICE = '{total_price}';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptRepository
     */
    protected $scriptRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptFactoryInterface
     */
    protected $scriptFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptRepository $scriptRepository
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptFactoryInterface $scriptFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ScriptRepository $scriptRepository,
        Setting $setting,
        ScriptFactoryInterface $scriptFactory
    ) {
        $this->em = $em;
        $this->scriptRepository = $scriptRepository;
        $this->setting = $setting;
        $this->scriptFactory = $scriptFactory;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Script\Script[]
     */
    public function getAll()
    {
        return $this->scriptRepository->getAll();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllQueryBuilder()
    {
        return $this->scriptRepository->getAllQueryBuilder();
    }

    /**
     * @param int $scriptId
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function getById($scriptId)
    {
        return $this->scriptRepository->getById($scriptId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $scriptData
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function create(ScriptData $scriptData)
    {
        $script = $this->scriptFactory->create($scriptData);

        $this->em->persist($script);
        $this->em->flush();

        return $script;
    }

    /**
     * @param int $scriptId
     * @param \Shopsys\FrameworkBundle\Model\Script\ScriptData $scriptData
     * @return \Shopsys\FrameworkBundle\Model\Script\Script
     */
    public function edit($scriptId, ScriptData $scriptData)
    {
        $script = $this->scriptRepository->getById($scriptId);

        $script->edit($scriptData);

        $this->em->persist($script);
        $this->em->flush();

        return $script;
    }

    /**
     * @param int $scriptId
     */
    public function delete($scriptId)
    {
        $script = $this->scriptRepository->getById($scriptId);

        $this->em->remove($script);
        $this->em->flush();
    }

    /**
     * @return string[]
     */
    public function getAllPagesScriptCodes()
    {
        $allPagesScripts = $this->scriptRepository->getScriptsByPlacement(Script::PLACEMENT_ALL_PAGES);
        $scriptCodes = [];

        foreach ($allPagesScripts as $script) {
            $scriptCodes[] = $script->getCode();
        }

        return $scriptCodes;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string[]
     */
    public function getOrderSentPageScriptCodesWithReplacedVariables(Order $order)
    {
        $scripts = $this->scriptRepository->getScriptsByPlacement(Script::PLACEMENT_ORDER_SENT_PAGE);
        $scriptCodes = [];

        foreach ($scripts as $script) {
            $scriptCodes[] = $this->replaceVariables($script->getCode(), $order);
        }

        return $scriptCodes;
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isGoogleAnalyticsActivated($domainId)
    {
        return !empty($this->setting->getForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $domainId));
    }

    /**
     * @param string|null $trackingId
     * @param int $domainId
     */
    public function setGoogleAnalyticsTrackingId($trackingId, $domainId)
    {
        $this->setting->setForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $trackingId, $domainId);
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getGoogleAnalyticsTrackingId($domainId)
    {
        return $this->setting->getForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $domainId);
    }

    /**
     * @param string $code
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    protected function replaceVariables($code, Order $order)
    {
        $variableReplacements = [
            self::VARIABLE_NUMBER => $order->getNumber(),
            self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat()->toValue(),
        ];

        return strtr($code, $variableReplacements);
    }
}
