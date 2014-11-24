<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Administrator\AdministratorFormType;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorData;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdministratorController extends Controller {

	/**
	 * @Route("/administrator/list/")
	 * @param Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Administrator::class, 'a');
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $gridFactory->create('administratorList', $dataSource);
		$grid->setDefaultOrder('realName');

		$grid->addColumn('realName', 'a.realName', 'Jméno', true);
		$grid->addColumn('email', 'a.email', 'Email');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_administrator_edit', array('id' => 'a.id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_administrator_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tohoto administrátora?');

		return $this->render('@SS6Shop/Admin/Content/Administrator/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/administrator/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$administratorFacade = $this->get('ss6.shop.administrator.administrator_facade');
		/* @var $administratorFacade \SS6\ShopBundle\Model\Administrator\AdministratorFacade */

		$administrator = $administratorFacade->getById($id);
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$form = $this->createForm(new AdministratorFormType(AdministratorFormType::SCENARIO_EDIT));

		$administratorData = new AdministratorData();

		if (!$form->isSubmitted()) {
			$administratorData->setFromEntity($administrator);
		}

		$form->setData($administratorData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			try {
				$administratorFacade->edit($id, $administratorData);

				$flashMessageSender->addSuccessTwig(
					'Byl upraven administrátor <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
						'name' => $administratorData->getRealName(),
						'url' => $this->generateUrl('admin_administrator_edit', array('id' => $administrator->getId())),
					)
				);
				return $this->redirect($this->generateUrl('admin_administrator_list'));

			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
				$flashMessageSender->addErrorTwig(
					'Administrátor s přihlašovacím jménem <strong>{{ name }}</strong> již existuje', array(
						'name' => $administratorData->getUsername(),
					)
				);
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace administrátora - ' . $administrator->getRealName()));

		return $this->render('@SS6Shop/Admin/Content/Administrator/edit.html.twig', array(
			'form' => $form->createView(),
			'administrator' => $administrator,
		));
	}

	/**
	 * @Route("/administrator/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$form = $this->createForm(new AdministratorFormType(AdministratorFormType::SCENARIO_CREATE));

		$administratorData = new AdministratorData();

		$form->setData($administratorData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$administratorData = $form->getData();
			$administratorFacade = $this->get('ss6.shop.administrator.administrator_facade');
			/* @var $administratorFacade \SS6\ShopBundle\Model\Administrator\AdministratorFacade */

			try {
				$administrator = $administratorFacade->create($administratorData);
				/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

				$flashMessageSender->addSuccessTwig(
					'Byl vytvořen administrátor <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
						'name' => $administrator->getRealName(),
						'url' => $this->generateUrl('admin_administrator_list', array('id' => $administrator->getId())),
					)
				);
				return $this->redirect($this->generateUrl('admin_administrator_list'));

			} catch (\SS6\ShopBundle\Model\Administrator\Exception\DuplicateUserNameException $ex) {
				$flashMessageSender->addErrorTwig(
					'Administrátor s přihlašovacím jménem <strong>{{ name }}</strong> již existuje', array(
						'name' => $administratorData->getUsername(),
					)
				);
			}

		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Administrator/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/administrator/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$administratorFacade = $this->get('ss6.shop.administrator.administrator_facade');
		/* @var $administratorFacade \SS6\ShopBundle\Model\Administrator\AdministratorFacade */

		$realName = $administratorFacade->getById($id)->getRealName();

		try {
			$administratorFacade->delete($id);
			$flashMessageSender->addSuccessTwig('Administrátor <strong>{{ name }}</strong> byl smazán.', array(
				'name' => $realName,
			));
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\DeletingSelfException $ex) {
			$flashMessageSender->addError('Nemůžete smazat sami sebe.');
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\DeletingLastAdministratorException $ex) {
			$flashMessageSender->addErrorTwig('Administrátor <strong>{{ name }}</strong> je jediný a nemůže být smazán.', array(
				'name' => $realName,
			));
		}

		return $this->redirect($this->generateUrl('admin_administrator_list'));
	}
}
