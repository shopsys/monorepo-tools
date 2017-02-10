<?php

namespace Shopsys\ShopBundle\Model\Slider;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Slider\SliderItem;

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
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem
     */
    public function getById($sliderItemId) {
        $sliderItem = $this->getSliderItemRepository()->find($sliderItemId);
        if ($sliderItem === null) {
            $message = 'Slider item with ID ' . $sliderItemId . ' not found.';
            throw new \Shopsys\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException($message);
        }
        return $sliderItem;
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem|null
     */
    public function findById($id) {
        return $this->getSliderItemRepository()->find($id);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem[]
     */
    public function getAll() {
        return $this->getSliderItemRepository()->findAll();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Slider\SliderItem[]
     */
    public function getAllVisibleByDomainId($domainId) {
        return $this->getSliderItemRepository()->findBy([
            'domainId' => $domainId,
            'hidden' => false,
        ]);
    }
}
