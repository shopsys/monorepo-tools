<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Model\Advert\AdvertFacade;

class AdvertController extends FrontBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Model\Advert\AdvertFacade
     */
    private $advertFacade;

    public function __construct(AdvertFacade $advertFacade) {
        $this->advertFacade = $advertFacade;
    }

    /**
     * @param string $positionName
     */
    public function boxAction($positionName) {
        $advert = $this->advertFacade->findRandomAdvertByPositionOnCurrentDomain($positionName);

        return $this->render('@ShopsysShop/Front/Content/Advert/box.html.twig', [
            'advert' => $advert,
        ]);
    }

}
