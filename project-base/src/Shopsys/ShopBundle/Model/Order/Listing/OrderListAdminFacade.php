<?php

namespace Shopsys\ShopBundle\Model\Order\Listing;

use Shopsys\ShopBundle\Model\Localization\Localization;

class OrderListAdminFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository
     */
    private $orderListAdminRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository $orderListAdminRepository
     * @param \Shopsys\ShopBundle\Model\Localization\Localization $localization
     */
    public function __construct(
        OrderListAdminRepository $orderListAdminRepository,
        Localization $localization
    ) {
        $this->orderListAdminRepository = $orderListAdminRepository;
        $this->localization = $localization;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilder()
    {
        return $this->orderListAdminRepository->getOrderListQueryBuilder($this->localization->getAdminLocale());
    }
}
