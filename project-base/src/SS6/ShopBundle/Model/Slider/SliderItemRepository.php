<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
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

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem|null
	 */
	public function findById($id) {
		return $this->getSliderItemRepository()->find($id);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem[]
	 */
	public function findAll() {
		return $this->getSliderItemRepository()->findAll();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem[]
	 */
	public function findAllByDomain(Domain $domain) {
		$domainId = $domain->getId();
		return $this->getSliderItemRepository()->findBy(array('domainId' => $domainId));
	}
}
