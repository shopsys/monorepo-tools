<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Slider\SliderItemRepository;

class SliderItemFacade {
	
	/**
	 * @var \Doctrine\ORM\EntityManager 
	 */
	private $em;

	/**
	 * @var type
	 */
	private $sliderItemRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemRepository $sliderItemRepository
	 */
	public function __construct(
		EntityManager $em,
		SliderItemRepository $sliderItemRepository
	) {
		$this->em = $em;
		$this->sliderItemRepository = $sliderItemRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 */
	public function create(SliderItemData $sliderItemData) {
		$sliderItem = new SliderItem($sliderItemData);

		$this->em->persist($sliderItem);
		$this->em->flush();

		return $sliderItem;
	}

	/**
	 * @param int $sliderItemId
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 */
	public function edit($sliderItemId, SliderItemData $sliderItemData) {
		$sliderItem = $this->sliderItemRepository->getById($sliderItemId);
		$sliderItem->edit($sliderItemData);

		$this->em->flush();

		return $sliderItem;
	}
}
