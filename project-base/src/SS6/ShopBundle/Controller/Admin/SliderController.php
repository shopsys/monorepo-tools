<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SliderController extends Controller {
	
	/**
	 * @Route("/slider/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		return $this->render('@SS6Shop/Admin/Content/Slider/list.html.twig');
	}

	/**
	 * @Route("/slider/item/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$sliderItemFormTypeFactory = $this->get('ss6.shop.form.admin.slider.slider_item_form_type_factory');
		/* @var $sliderItemFormTypeFactory SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory */

		$form = $this->createForm($sliderItemFormTypeFactory->create());
		$sliderItemData = new SliderItemData();

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
			/* @var $sliderItemFacade SS6\ShopBundle\Model\Slider\SliderItemFacade */
			$sliderItem = $sliderItemFacade->create($form->getData());

			$flashMessageSender->addSuccessTwig('Byla vytvořena stránka slideru'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $sliderItem->getName(),
				'url' => $this->generateUrl('admin_slider_list', array('id' => $sliderItem->getId())), //admin_slider_edit!!!
			));
			return $this->redirect($this->generateUrl('admin_slider_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Slider/new.html.twig', array(
			'form' => $form->createView(),
		));

	}
	
}
