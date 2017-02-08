<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Model\Customer\UserRepository;

class CustomerListAdminFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	public function __construct(UserRepository $userRepository) {
		$this->userRepository = $userRepository;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
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
