<?php

namespace SS6\CoreBundle\Model\Payment\Repository;

use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository {
	/**
	 * @param array|null $orderBy
	 * @return array
	 */
	public function getAllUndeleted($orderBy = null) {
		return $this->findBy(array('deleted' => false), $orderBy);
	}
}