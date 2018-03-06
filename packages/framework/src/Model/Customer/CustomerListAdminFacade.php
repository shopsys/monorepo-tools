<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;

class CustomerListAdminFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
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
