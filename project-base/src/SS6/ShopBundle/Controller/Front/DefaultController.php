<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
		/* @var $sliderItemFacade \SS6\ShopBundle\Model\Slider\SliderItemFacade */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$sliderItems = $sliderItemFacade->findAllByDomain($domain);

		return $this->render('@SS6Shop/Front/Content/Default/index.html.twig', array(
			'sliderItems' => $sliderItems,
		));
	}

}
