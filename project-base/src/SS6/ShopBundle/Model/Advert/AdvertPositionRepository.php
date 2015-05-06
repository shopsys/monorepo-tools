<?php

namespace SS6\ShopBundle\Model\Advert;

class AdvertPositionRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertPosition[]
	 */
	private $advertPositionsByName;

	public function __construct(array $advertPositions) {
		foreach ($advertPositions as $advertPosition) {
			$this->advertPositionsByName[$advertPosition->getName()] = $advertPosition;
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Advert\AdvertPosition[positionName]
	 */
	public function getPositionsIndexedByName() {
		return $this->advertPositionsByName;
	}

}