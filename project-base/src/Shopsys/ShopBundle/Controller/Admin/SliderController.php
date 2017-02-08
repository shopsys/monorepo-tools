<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Slider\SliderItem;
use Shopsys\ShopBundle\Model\Slider\SliderItemData;
use Shopsys\ShopBundle\Model\Slider\SliderItemFacade;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Form\Admin\Slider\SliderItemFormTypeFactory
	 */
	private $sliderItemFormTypeFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Slider\SliderItemFacade
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

		$grid->addColumn('name', 's.name', t('Name'));
		$grid->addColumn('link', 's.link', t('Link'));
		$grid->addEditActionColumn('admin_slider_edit', ['id' => 's.id']);
		$grid->addDeleteActionColumn('admin_slider_delete', ['id' => 's.id'])
			->setConfirmMessage(t('Do you really want to remove this page?'));

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
		$sliderItemData->domainId = $this->selectedDomain->getId();

		$form->setData($sliderItemData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$sliderItem = $this->sliderItemFacade->create(
				$form->getData(),
				$this->selectedDomain->getId()
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Slider page <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
				[
					'name' => $sliderItem->getName(),
					'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
				]
			);
			return $this->redirectToRoute('admin_slider_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
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
				t('Slider page <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
				[
					'name' => $sliderItem->getName(),
					'url' => $this->generateUrl('admin_slider_edit', ['id' => $sliderItem->getId()]),
				]
			);

			return $this->redirectToRoute('admin_slider_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
		}

		$this->breadcrumb->overrideLastItem(
			new MenuItem(t('Editing slider page - %name%', ['%name%' => $sliderItem->getName()]))
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
				t('Page <strong>{{ name }}</strong> deleted'),
				[
					'name' => $name,
				]
			);
		} catch (\Shopsys\ShopBundle\Model\Slider\Exception\SliderItemNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected page doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_slider_list');

	}
}
