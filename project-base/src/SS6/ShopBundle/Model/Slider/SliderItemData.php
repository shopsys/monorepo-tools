<?php

namespace SS6\ShopBundle\Model\Slider;

class SliderItemData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $link;

	/**
	 * @var string
	 */
	public $image;

	/**
	 * @param string $name
	 * @param string $link
	 * @param string $image
	 */
	public function __construct(
		$name = null,
		$link = null,
		$image = null
	) {
		$this->name = $name;
		$this->link = $link;
		$this->image = $image;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItem $sliderItem
	 */
	public function setFromEntity(SliderItem $sliderItem) {
		$this->name = $sliderItem->getName();
		$this->link = $sliderItem->getLink();
	}
}
