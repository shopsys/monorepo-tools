<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Slider\SliderItemRepository;

class SliderItemFacade {
	
	/**
	 * @var \Doctrine\ORM\EntityManager 
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Slider\SliderItemRepository
	 */
	private $sliderItemRepository;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemRepository $sliderItemRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 */
	public function __construct(
		EntityManager $em,
		SliderItemRepository $sliderItemRepository,
		SelectedDomain $selectedDomain
	) {
		$this->em = $em;
		$this->sliderItemRepository = $sliderItemRepository;
		$this->selectedDomain = $selectedDomain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 */
	public function create(SliderItemData $sliderItemData) {
		$sliderItem = new SliderItem($sliderItemData, $this->selectedDomain->getId());

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

	/**
	 * @param int $sliderItemId
	 */
	public function delete($sliderItemId) {
		$sliderItem = $this->sliderItemRepository->getById($sliderItemId);

		$this->em->remove($sliderItem);
		$this->em->flush();
	}
}
