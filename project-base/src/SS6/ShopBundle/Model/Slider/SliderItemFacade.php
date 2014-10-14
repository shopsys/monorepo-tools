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

	public function __construct(
		EntityManager $em,
		SliderItemRepository $sliderItemRepository
	) {
		$this->em = $em;
		$this->sliderItemRepository = $sliderItemRepository;
	}

	public function create(SliderItemData $sliderItemData) {
		$sliderItem = new SliderItem($sliderItemData);

		$this->em->persist($sliderItem);
		$this->em->flush();

		return $sliderItem;
	}
}
