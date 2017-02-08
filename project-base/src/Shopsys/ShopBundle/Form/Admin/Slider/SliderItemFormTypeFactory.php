<?php

namespace SS6\ShopBundle\Form\Admin\Slider;

use SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType;

class SliderItemFormTypeFactory {

	/**
	 * @param bool $scenarioCreate
	 * @return \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType
	 */
	public function create($scenarioCreate = false) {
		return new SliderItemFormType($scenarioCreate);
	}
}
