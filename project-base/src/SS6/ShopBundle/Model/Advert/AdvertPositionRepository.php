<?php

namespace SS6\ShopBundle\Model\Advert;

class AdvertPositionRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertPosition[]
	 */
	private $advertPositions;

	public function __construct(array $advertPositions) {
		$this->advertPositions = $advertPositions;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Advert\AdvertPosition[]
	 */
	public function getAll() {
		return $this->advertPositions;
	}

	/**
	 * @param string $name
	 * @return \SS6\ShopBundle\Model\Advert\AdvertPosition[positionName]
	 */
	public function getPositionsByName() {
		$advertPositionsByName = [];
		foreach ($this->advertPositions as $advertPosition) {
			$advertPositionsByName[$advertPosition->getName()] = $advertPosition;
		}
		return $advertPositionsByName;
	}

}
