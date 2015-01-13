<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Grid\Ordering\OrderableEntityInterface;
use SS6\ShopBundle\Model\Image\EntityWithImagesInterface;
use SS6\ShopBundle\Model\Slider\SliderItemData;

/**
 * SliderItem
 *
 * @ORM\Table(name="slider_items")
 * @ORM\Entity
 */
class SliderItem implements OrderableEntityInterface, EntityWithImagesInterface {

	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $link;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 */
	public function __construct(SliderItemData $sliderItemData, $domainId) {
		$this->domainId = $domainId;
		$this->name = $sliderItemData->name;
		$this->link = $sliderItemData->link;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 */
	public function edit(SliderItemData $sliderItemData) {
		$this->name = $sliderItemData->name;
		$this->link = $sliderItemData->link;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return int|null
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}



}
