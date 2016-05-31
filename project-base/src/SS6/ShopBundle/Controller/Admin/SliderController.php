<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Slider\SliderItem;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use SS6\ShopBundle\Model\Slider\SliderItemFacade;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory
	 */
	private $sliderItemFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Slider\SliderItemFacade
	 */
	private $sliderItemFacade;

	public function __construct(
		SliderItemFacade $sliderItemFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		SliderItemFormTypeFactory $sliderItemFormTypeFactory,
		Breadcrumb $breadcrumb
	) {
		$this->sliderItemFacade = $sliderItemFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->sliderItemFormTypeFactory = $sliderItemFormTypeFactory;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/slider/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('s')
			->from(SliderItem::class, 's')
			->where('s.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 's.id');

		$grid = $this->gridFactory->create('sliderItemList', $dataSource);
		$grid->enableDragAndDrop(SliderItem::class);

		$grid->addColumn('name', 's.name', t('Název'));
		$grid->addColumn('link', 's.link', t('Odkaz'));
		$grid->addEditActionColumn('admin_slider_edit', ['id' => 's.id']);
		$grid->addDeleteActionColumn('admin_slider_delete', ['id' => 's.id'])
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
		$form = $this->createForm($this->sliderItemFormTypeFactory->create(true));
		$sliderItemData = new SliderItemData();

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItem = $this->sliderItemFacade->create($form->getData());

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Byla vytvořena stránka slideru <strong><a href="{{ url }}">{{ name }}</a></strong>'),
				[
					'name' => $sliderItem->getName(),
					'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
				]
			);
			return $this->redirectToRoute('admin_slider_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Admin/Content/Slider/new.html.twig', [
			'form' => $form->createView(),
			'selectedDomainId' => $this->selectedDomain->getId(),
		]);

	}

	/**
	 * @Route("/slider/item/edit/{id}", requirements={"id"="\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$sliderItem = $this->sliderItemFacade->getById($id);
		$form = $this->createForm($this->sliderItemFormTypeFactory->create());
		$sliderItemData = new SliderItemData();
		$sliderItemData->setFromEntity($sliderItem);

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->sliderItemFacade->edit($id, $sliderItemData);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Byla upravena stránka slideru <strong><a href="{{ url }}">{{ name }}</a></strong>'),
				[
					'name' => $sliderItem->getName(),
					'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
				]
			);

			return $this->redirectToRoute('admin_slider_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$this->breadcrumb->overrideLastItem(
			new MenuItem(t('Editace stránky slideru - %name%', ['%name%' => $sliderItem->getName()]))
		);

		return $this->render('@SS6Shop/Admin/Content/Slider/edit.html.twig', [
			'form' => $form->createView(),
			'sliderItem' => $sliderItem,
		]);
	}

	/**
	 * @Route("/slider/item/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$name = $this->sliderItemFacade->getById($id)->getName();

			$this->sliderItemFacade->delete($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Stránka <strong>{{ name }}</strong> byla smazána'),
				[
					'name' => $name,
				]
			);
		} catch (\SS6\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolená stránka neexistuje.'));
		}

		return $this->redirectToRoute('admin_slider_list');

	}
}
