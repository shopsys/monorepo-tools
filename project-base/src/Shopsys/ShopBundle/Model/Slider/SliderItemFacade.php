<?php

namespace Shopsys\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Model\Slider\SliderItemRepository;

class SliderItemFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Slider\SliderItemRepository
     */
    private $sliderItemRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        EntityManager $em,
        SliderItemRepository $sliderItemRepository,
        ImageFacade $imageFacade,
        Domain $domain
    ) {
        $this->em = $em;
        $this->sliderItemRepository = $sliderItemRepository;
        $this->imageFacade = $imageFacade;
        $this->domain = $domain;
    }

    /**
     * @param int $sliderItemId
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem
     */
    public function getById($sliderItemId) {
        return $this->sliderItemRepository->getById($sliderItemId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Slider\SliderItemData $sliderItemData
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem
     */
    public function create(SliderItemData $sliderItemData, $domainId) {
        $sliderItem = new SliderItem($sliderItemData, $domainId);

        $this->em->persist($sliderItem);
        $this->em->flush();
        $this->imageFacade->uploadImage($sliderItem, $sliderItemData->image, null);

        return $sliderItem;
    }

    /**
     * @param int $sliderItemId
     * @param \Shopsys\ShopBundle\Model\Slider\SliderItemData $sliderItemData
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem
     */
    public function edit($sliderItemId, SliderItemData $sliderItemData) {
        $sliderItem = $this->sliderItemRepository->getById($sliderItemId);
        $sliderItem->edit($sliderItemData);

        $this->em->flush();
        $this->imageFacade->uploadImage($sliderItem, $sliderItemData->image, null);

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
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem[]
     */
    public function getAllVisibleOnCurrentDomain() {
        return $this->sliderItemRepository->getAllVisibleByDomainId($this->domain->getId());
    }
}
