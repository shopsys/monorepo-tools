<?php

namespace SS6\ShopBundle\Model\Slider\Grid;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Grid\DragAndDrop\GridOrderingInterface;
use SS6\ShopBundle\Model\Slider\SliderItem;
use SS6\ShopBundle\Model\Slider\SliderItemRepository;

class DragAndDropOrderingService implements GridOrderingInterface {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Slider\SliderItemRepository
	 */
	private $sliderItemRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Slider\SliderItemRepository $sliderItemRepository
	 */
	public function __construct(EntityManager $em, SliderItemRepository $sliderItemRepository) {
		$this->em = $em;
		$this->sliderItemRepository = $sliderItemRepository;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.slider.grid.drag_and_drop_ordering_service';
	}

	/**
	 * @param array $rowIds
	 */
	public function saveOrder(array $rowIds) {
		$position = 0;

		foreach ($rowIds as $rowId) {
			$sliderItem = $this->sliderItemRepository->findById($rowId);

			if ($sliderItem instanceof SliderItem) {
				$sliderItem->setPosition($position);
			}

			$position++;
		}

		$this->em->flush();
	}

}
