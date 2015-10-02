<?php

namespace SS6\ShopBundle\Component\Grid\Ordering;

use SS6\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface;

class GridOrderingService {

	/**
	 * @param \SS6\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface|null $entity
	 * @param int $position
	 */
	public function setPosition($entity, $position) {
		if ($entity instanceof OrderableEntityInterface) {
			$entity->setPosition($position);
		} else {
			throw new \SS6\ShopBundle\Component\Grid\Ordering\Exception\EntityIsNotOrderableException();
		}
	}

}
