<?php

namespace Shopsys\ShopBundle\Form\Admin\Advert;

use Shopsys\ShopBundle\Model\Advert\Advert;
use Shopsys\ShopBundle\Model\Advert\AdvertPositionList;
use Shopsys\ShopBundle\Twig\ImageExtension;

class AdvertFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Model\Advert\AdvertPositionList
     */
    private $advertPositionList;

    /**
     * @var \Shopsys\ShopBundle\Twig\ImageExtension
     */
    private $imageExtension;

    public function __construct(
        ImageExtension $imageExtension,
        AdvertPositionList $advertPositionList
    ) {
        $this->imageExtension = $imageExtension;
        $this->advertPositionList = $advertPositionList;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Advert\Advert $advert
     * @return \Shopsys\ShopBundle\Form\Admin\Advert\AdvertFormType
     */
    public function create(Advert $advert = null) {
        $imageExists = false;
        if ($advert !== null) {
            $imageExists = $this->imageExtension->imageExists($advert);
        }

        return new AdvertFormType(
            $imageExists,
            $this->advertPositionList->getTranslationsIndexedByValue()
        );
    }

}
