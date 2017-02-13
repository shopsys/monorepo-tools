<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\ShopBundle\Model\Customer\UserRepository;

class CustomerListAdminFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCustomerListQueryBuilderByQuickSearchData(
        $domainId,
        QuickSearchFormData $quickSearchData
    ) {
        return $this->userRepository->getCustomerListQueryBuilderByQuickSearchData(
            $domainId,
            $quickSearchData
        );
    }
}
