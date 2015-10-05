<?php

namespace SS6\ShopBundle\Model\Product\Unit;

use SS6\ShopBundle\Model\Product\Unit\Unit;
use SS6\ShopBundle\Model\Product\Unit\UnitData;

class UnitService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function create(UnitData $unitData) {
		return new Unit($unitData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Unit\Unit $unit
	 * @param \SS6\ShopBundle\Model\Product\Unit\UnitData $unitData
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function edit(Unit $unit, UnitData $unitData) {
		$unit->edit($unitData);

		return $unit;
	}

}
