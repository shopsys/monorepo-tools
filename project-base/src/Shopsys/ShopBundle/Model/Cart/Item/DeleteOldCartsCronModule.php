<?php

namespace Shopsys\ShopBundle\Model\Cart\Item;

use Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Symfony\Bridge\Monolog\Logger;

class DeleteOldCartsCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    public function __construct(CartFacade $cartFacade)
    {
        $this->cartFacade = $cartFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->cartFacade->deleteOldCarts();
    }
}
