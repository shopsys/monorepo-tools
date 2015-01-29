<?php

namespace SS6\ShopBundle\Model\Product\Flag;

use SS6\ShopBundle\Model\Product\Flag\Flag;
use SS6\ShopBundle\Model\Product\Flag\FlagData;

class FlagService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag
	 */
	public function create(FlagData $flagData) {
		return new Flag($flagData);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag $flag
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag
	 */
	public function edit(Flag $flag, FlagData $flagData) {
		$flag->edit($flagData);

		return $flag;
	}

}
