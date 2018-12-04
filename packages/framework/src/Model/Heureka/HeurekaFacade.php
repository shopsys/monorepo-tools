<?php

namespace Shopsys\FrameworkBundle\Model\Heureka;

use Exception;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Symfony\Bridge\Monolog\Logger;

class HeurekaFacade
{
    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaShopCertificationFactory
     */
    protected $heurekaShopCertificationFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaShopCertificationLocaleHelper
     */
    protected $heurekaShopCertificationLocaleHelper;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting
     */
    protected $heurekaSetting;

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaShopCertificationFactory $heurekaShopCertificationFactory
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaShopCertificationLocaleHelper $heurekaShopCertificationLocaleHelper
     * @param \Shopsys\FrameworkBundle\Model\Heureka\HeurekaSetting $heurekaSetting
     */
    public function __construct(
        Logger $logger,
        HeurekaShopCertificationFactory $heurekaShopCertificationFactory,
        HeurekaShopCertificationLocaleHelper $heurekaShopCertificationLocaleHelper,
        HeurekaSetting $heurekaSetting
    ) {
        $this->logger = $logger;
        $this->heurekaShopCertificationFactory = $heurekaShopCertificationFactory;
        $this->heurekaShopCertificationLocaleHelper = $heurekaShopCertificationLocaleHelper;
        $this->heurekaSetting = $heurekaSetting;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function sendOrderInfo(Order $order)
    {
        try {
            $heurekaShopCertification = $this->heurekaShopCertificationFactory->create($order);
            $heurekaShopCertification->logOrder();
        } catch (\Shopsys\FrameworkBundle\Model\Heureka\Exception\LocaleNotSupportedException $ex) {
            $this->logError($ex, $order);
        } catch (\Heureka\ShopCertification\Exception $ex) {
            $this->logError($ex, $order);
        }
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isHeurekaShopCertificationActivated($domainId)
    {
        return $this->heurekaSetting->isHeurekaShopCertificationActivated($domainId);
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function isHeurekaWidgetActivated($domainId)
    {
        return $this->heurekaSetting->isHeurekaWidgetActivated($domainId);
    }

    /**
     * @param string $locale
     * @return bool
     */
    public function isDomainLocaleSupported($locale)
    {
        return $this->heurekaShopCertificationLocaleHelper->isDomainLocaleSupported($locale);
    }

    /**
     * @param string $locale
     * @return string|null
     */
    public function getServerNameByLocale($locale)
    {
        return $this->heurekaShopCertificationLocaleHelper->getServerNameByLocale($locale);
    }

    /**
     * @param \Exception $ex
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    protected function logError(Exception $ex, Order $order)
    {
        $message = 'Sending order (ID = "' . $order->getId() . '") to Heureka failed - ' . get_class($ex) . ': ' . $ex->getMessage();
        $this->logger->error($message, ['exceptionFullInfo' => (string)$ex]);
    }
}
