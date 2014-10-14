<?php

namespace SS6\ShopBundle\Model\Slider;

class SliderItemData {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $link;

	/**
	 * @var string
	 */
	private $image;

	/**
	 * @param string $name
	 * @param string $link
	 * @param string $image
	 */
	public function __construct(
		$name,
		$link,
		$image
	) {
		$this->name = $name;
		$this->link = $link;
		$this->image = $image;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItem $sliderItem
	 */
	public function setFromEntity(SliderItem $sliderItem) {
		$this->name = $sliderItem->getName();
		$this->link = $sliderItem->getLink();
		$this->image = $sliderItem->getImage();
	}
}
