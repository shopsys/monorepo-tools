<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$sliderItemRepository = $this->get('ss6.shop.slider.slider_item_repository');
		/* @var $sliderItemRepository SS6\ShopBundle\Model\Slider\SliderItemRepository */
		$sliderItems = $sliderItemRepository->findAll();

		return $this->render('@SS6Shop/Front/Content/Default/index.html.twig', array(
			'sliderItems' => $sliderItems,
		));
	}

}
