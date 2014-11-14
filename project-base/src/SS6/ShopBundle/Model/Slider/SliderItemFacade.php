<?php

namespace SS6\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Image\ImageFacade;
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
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemRepository $sliderItemRepository
	 * @param \SS6\ShopBundle\Model\Domain\SelectedDomain $selectedDomain
	 */
	public function __construct(
		EntityManager $em,
		SliderItemRepository $sliderItemRepository,
		SelectedDomain $selectedDomain,
		ImageFacade	$imageFacade
	) {
		$this->em = $em;
		$this->sliderItemRepository = $sliderItemRepository;
		$this->selectedDomain = $selectedDomain;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param int $sliderItemId
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 */
	public function getById($sliderItemId) {
		return $this->sliderItemRepository->getById($sliderItemId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemData $sliderItemData
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem
	 */
	public function create(SliderItemData $sliderItemData) {
		$sliderItem = new SliderItem($sliderItemData, $this->selectedDomain->getId());

		$this->em->beginTransaction();
		$this->em->persist($sliderItem);
		$this->em->flush();
		$this->imageFacade->uploadImage($sliderItem, $sliderItemData->getImage(), null);
		$this->em->commit();

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

		$this->em->beginTransaction();
		$this->em->flush();
		$this->imageFacade->uploadImage($sliderItem, $sliderItemData->getImage(), null);
		$this->em->commit();

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

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 * @return \SS6\ShopBundle\Model\Slider\SliderItem[]
	 */
	public function findAllByDomain(Domain $domain) {
		return $this->sliderItemRepository->findAllByDomain($domain);
	}
}
