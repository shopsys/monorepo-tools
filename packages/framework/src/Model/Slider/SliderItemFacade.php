<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;

class SliderItemFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Slider\SliderItemRepository
     */
    protected $sliderItemRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Slider\SliderItemFactoryInterface
     */
    protected $sliderItemFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemRepository $sliderItemRepository
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemFactoryInterface $sliderItemFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        SliderItemRepository $sliderItemRepository,
        ImageFacade $imageFacade,
        Domain $domain,
        SliderItemFactoryInterface $sliderItemFactory
    ) {
        $this->em = $em;
        $this->sliderItemRepository = $sliderItemRepository;
        $this->imageFacade = $imageFacade;
        $this->domain = $domain;
        $this->sliderItemFactory = $sliderItemFactory;
    }

    /**
     * @param int $sliderItemId
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem
     */
    public function getById($sliderItemId)
    {
        return $this->sliderItemRepository->getById($sliderItemId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemData $sliderItemData
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem
     */
    public function create(SliderItemData $sliderItemData)
    {
        $sliderItem = $this->sliderItemFactory->create($sliderItemData);

        $this->em->persist($sliderItem);
        $this->em->flush();
        $this->imageFacade->uploadImage($sliderItem, $sliderItemData->image->uploadedFiles, null);

        return $sliderItem;
    }

    /**
     * @param int $sliderItemId
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemData $sliderItemData
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem
     */
    public function edit($sliderItemId, SliderItemData $sliderItemData)
    {
        $sliderItem = $this->sliderItemRepository->getById($sliderItemId);
        $sliderItem->edit($sliderItemData);

        $this->em->flush();
        $this->imageFacade->uploadImage($sliderItem, $sliderItemData->image->uploadedFiles, null);

        return $sliderItem;
    }

    /**
     * @param int $sliderItemId
     */
    public function delete($sliderItemId)
    {
        $sliderItem = $this->sliderItemRepository->getById($sliderItemId);

        $this->em->remove($sliderItem);
        $this->em->flush();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem[]
     */
    public function getAllVisibleOnCurrentDomain()
    {
        return $this->sliderItemRepository->getAllVisibleByDomainId($this->domain->getId());
    }
}
