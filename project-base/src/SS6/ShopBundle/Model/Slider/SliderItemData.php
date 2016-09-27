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
	 * @var bool|null
	 */
	public $hidden;

	/**
	 * @param string $name
	 * @param string $link
	 * @param string $image
	 */
	public function __construct(
		$name = null,
		$link = null,
		$image = null,
		$hidden = false
	) {
		$this->name = $name;
		$this->link = $link;
		$this->image = $image;
		$this->hidden = $hidden;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItem $sliderItem
	 */
	public function setFromEntity(SliderItem $sliderItem) {
		$this->name = $sliderItem->getName();
		$this->link = $sliderItem->getLink();
		$this->hidden = $sliderItem->isHidden();
	}
}
