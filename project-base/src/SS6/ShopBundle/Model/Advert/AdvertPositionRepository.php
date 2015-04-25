<?php

namespace SS6\ShopBundle\Model\Advert;

class AdvertPositionRepository {

	/**
	 * @var array
	 */
	private $advertPositions;

	public function __construct(array $advertPositions) {
		$this->advertPositions = $advertPositions;
	}

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->advertPositions;
	}

	/**
	 * @param string $nama
	 */
	public function getByName($name) {
		$this->advertPositions[$name];
	}

}
