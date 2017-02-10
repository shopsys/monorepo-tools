<?php

namespace Shopsys\ShopBundle\Model\Script;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Script\ScriptRepository;

class ScriptFacade
{
    const VARIABLE_NUMBER = '{number}';
    const VARIABLE_TOTAL_PRICE = '{total_price}';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Script\ScriptRepository
     */
    private $scriptRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Script\ScriptRepository $scriptRepository
     */
    public function __construct(
        EntityManager $em,
        ScriptRepository $scriptRepository,
        Setting $setting
    ) {
        $this->em = $em;
        $this->scriptRepository = $scriptRepository;
        $this->setting = $setting;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Script\Script[]
     */
    public function getAll() {
        return $this->scriptRepository->getAll();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllQueryBuilder() {
        return $this->scriptRepository->getAllQueryBuilder();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Script\Script
     */
    public function getById($scriptId) {
        return $this->scriptRepository->getById($scriptId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Script\ScriptData $scriptData
     * @return \Shopsys\ShopBundle\Model\Script\Script
     */
    public function create(ScriptData $scriptData) {
        $script = new Script($scriptData);

        $this->em->persist($script);
        $this->em->flush();

        return $script;
    }

    /**
     * @param int $scriptId
     * @param \Shopsys\ShopBundle\Model\Script\ScriptData $scriptData
     * @return \Shopsys\ShopBundle\Model\Script\Script
     */
    public function edit($scriptId, ScriptData $scriptData) {
        $script = $this->scriptRepository->getById($scriptId);

        $script->edit($scriptData);

        $this->em->persist($script);
        $this->em->flush();

        return $script;
    }

    /**
     * @param int $scriptId
     */
    public function delete($scriptId) {
        $script = $this->scriptRepository->getById($scriptId);

        $this->em->remove($script);
        $this->em->flush();
    }

    /**
     * @return string[]
     */
    public function getAllPagesScriptCodes() {
        $allPagesScripts = $this->scriptRepository->getScriptsByPlacement(Script::PLACEMENT_ALL_PAGES);
        $scriptCodes = [];

        foreach ($allPagesScripts as $script) {
            $scriptCodes[] = $script->getCode();
        }

        return $scriptCodes;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @return string[]
     */
    public function getOrderSentPageScriptCodesWithReplacedVariables(Order $order) {
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
    public function isGoogleAnalyticsActivated($domainId) {
        return !empty($this->setting->getForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $domainId));
    }

    /**
     * @param string|null $trackingId
     * @param int $domainId
     */
    public function setGoogleAnalyticsTrackingId($trackingId, $domainId) {
        $this->setting->setForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $trackingId, $domainId);
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getGoogleAnalyticsTrackingId($domainId) {
        return $this->setting->getForDomain(Script::GOOGLE_ANALYTICS_TRACKING_ID_SETTING_NAME, $domainId);
    }

    /**
     * @param string $code
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @return string
     */
    private function replaceVariables($code, Order $order) {
        $variableReplacements = [
            self::VARIABLE_NUMBER => $order->getNumber(),
            self::VARIABLE_TOTAL_PRICE => $order->getTotalPriceWithVat(),
        ];

        return strtr($code, $variableReplacements);
    }
}
