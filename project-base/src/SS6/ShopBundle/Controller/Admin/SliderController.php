<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Slider\SliderItem;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SliderController extends Controller {
	
	/**
	 * @Route("/slider/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('s')
			->from(SliderItem::class, 's');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

		$grid = $gridFactory->create('sliderItemList', $dataSource);
		$grid->addColumn('name', 's.name', 'Název');
		$grid->addColumn('link', 's.link', 'Odkaz');
		$grid->addActionColumn('edit', 'Upravit', 'admin_slider_edit', array('id' => 's.id'));
//		$grid->addActionColumn('delete', 'Smazat', 'admin_slider_delete', array('id' => 's.id'))
//			->setConfirmMessage('Opravdu chcete odstranit tuto stránku?');

		return $this->render('@SS6Shop/Admin/Content/Slider/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
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
				'url' => $this->generateUrl('admin_slider_edit', array('id' => $sliderItem->getId())),
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
	
	/**
	 * @Route("/slider/item/edit/{id}", requirements={"id"="\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$sliderItemRepository = $this->get('ss6.shop.slider.slider_item_repository');
		/* @var $sliderItemRepository SS6\ShopBundle\Model\Slider\SliderItemRepository */
		$sliderItemFormTypeFactory = $this->get('ss6.shop.form.admin.slider.slider_item_form_type_factory');
		/* @var $sliderItemFormTypeFactory SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory */

		$sliderItem = $sliderItemRepository->getById($id);
		$form = $this->createForm($sliderItemFormTypeFactory->create());
		$sliderItemData = new SliderItemData();

		if (!$form->isSubmitted()) {
			$sliderItemData->setFromEntity($sliderItem);
		}

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItemData = $form->getData();

			$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
			/* @var $sliderItemFacade SS6\ShopBundle\Model\Slider\SliderItemFacade */
			$sliderItem = $sliderItemFacade->edit($id, $sliderItemData);

			$flashMessageSender->addSuccessTwig('Byla upravena stránka slideru <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $sliderItem->getName(),
				'url' =>  $this->generateUrl('admin_slider_edit', array('id' => $sliderItem->getId())),				
			));
			return $this->redirect($this->generateUrl('admin_slider_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace stránky slideru - ' . $sliderItem->getName()));

		return $this->render('@SS6Shop/Admin/Content/Slider/edit.html.twig', array(
			'form' => $form->createView(),
			'sliderItem' => $sliderItem,
		));
	}
}
