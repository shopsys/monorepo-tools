<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Slider\SliderItem;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends Controller {

	/**
	 * @Route("/slider/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('s')
			->from(SliderItem::class, 's')
			->where('s.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

		$grid = $gridFactory->create('sliderItemList', $dataSource);
		$grid->enableDragAndDrop(SliderItem::class);

		$grid->addColumn('name', 's.name', 'Název');
		$grid->addColumn('link', 's.link', 'Odkaz');
		$grid->addActionColumn('edit', 'Upravit', 'admin_slider_edit', ['id' => 's.id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_slider_delete', ['id' => 's.id'])
			->setConfirmMessage('Opravdu chcete odstranit tuto stránku?');

		$grid->setTheme('@SS6Shop/Admin/Content/Slider/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Slider/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/slider/item/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$sliderItemFormTypeFactory = $this->get('ss6.shop.form.admin.slider.slider_item_form_type_factory');
		/* @var $sliderItemFormTypeFactory \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$form = $this->createForm($sliderItemFormTypeFactory->create(true));
		$sliderItemData = new SliderItemData();

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
			/* @var $sliderItemFacade SS6\ShopBundle\Model\Slider\SliderItemFacade */
			$sliderItem = $sliderItemFacade->create($form->getData());

			$flashMessageSender->addSuccessFlashTwig('Byla vytvořena stránka slideru'
					. ' <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $sliderItem->getName(),
				'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_slider_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Slider/new.html.twig', [
			'form' => $form->createView(),
			'selectedDomainId' => $selectedDomain->getId(),
		]);

	}

	/**
	 * @Route("/slider/item/edit/{id}", requirements={"id"="\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
		/* @var $sliderItemFacade \SS6\ShopBundle\Model\Slider\SliderItemFacade */
		$sliderItemFormTypeFactory = $this->get('ss6.shop.form.admin.slider.slider_item_form_type_factory');
		/* @var $sliderItemFormTypeFactory \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory */

		$sliderItem = $sliderItemFacade->getById($id);
		$form = $this->createForm($sliderItemFormTypeFactory->create());
		$sliderItemData = new SliderItemData();
		$sliderItemData->setFromEntity($sliderItem);

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItemFacade->edit($id, $sliderItemData);

			$flashMessageSender->addSuccessFlashTwig(
				'Byla upravena stránka slideru <strong><a href="{{ url }}">{{ name }}</a></strong>',
				[
					'name' => $sliderItem->getName(),
					'url' =>  $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
				]
			);
			return $this->redirect($this->generateUrl('admin_slider_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace stránky slideru - ' . $sliderItem->getName()));

		return $this->render('@SS6Shop/Admin/Content/Slider/edit.html.twig', [
			'form' => $form->createView(),
			'sliderItem' => $sliderItem,
		]);
	}

	/**
	 * @Route("/slider/item/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$sliderItemFacade = $this->get('ss6.shop.slider.slider_item_facade');
		/* @var $sliderItemFacade SS6\ShopBundle\Model\Slider\SliderItemFacade */

		try {
			$name = $sliderItemFacade->getById($id)->getName();
			$sliderItemFacade->delete($id);

			$flashMessageSender->addSuccessFlashTwig('Stránka <strong>{{ name }}</strong> byla smazána', [
				'name' => $name,
			]);
		} catch (\SS6\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená stránka neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_slider_list'));

	}
}
