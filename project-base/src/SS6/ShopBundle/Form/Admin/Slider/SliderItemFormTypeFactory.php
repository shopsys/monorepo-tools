<?php

namespace SS6\ShopBundle\Form\Admin\Slider;

use SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType;

class SliderItemFormTypeFactory {

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormType
	 * @param bool $scenarioCreate
	 */
	public function create($scenarioCreate = false) {
		return new SliderItemFormType($scenarioCreate);
	}
}
