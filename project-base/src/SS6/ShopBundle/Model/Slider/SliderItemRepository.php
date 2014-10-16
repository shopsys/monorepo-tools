<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Slider\SliderItem;

class SliderItemRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getSliderItemRepository() {
		return $this->em->getRepository(SliderItem::class);
	}

	/**
	 * @param int $sliderItemId
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 * @throws \SS6\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException
	 */
	public function getById($sliderItemId) {
		$criteria = array('id' => $sliderItemId);
		$sliderItem = $this->getSliderItemRepository()->findOneBy($criteria);
		if ($sliderItem === null) {
			throw new \SS6\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException($criteria);
		}
		return $sliderItem;
	}
}
