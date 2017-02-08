<?php

namespace Shopsys\ShopBundle\Form\Admin\Slider;

use Shopsys\ShopBundle\Form\Admin\Slider\SliderItemFormType;

class SliderItemFormTypeFactory {

	/**
	 * @param bool $scenarioCreate
	 * @return \Shopsys\ShopBundle\Form\Admin\Slider\SliderItemFormType
	 */
	public function create($scenarioCreate = false) {
		return new SliderItemFormType($scenarioCreate);
	}
}
