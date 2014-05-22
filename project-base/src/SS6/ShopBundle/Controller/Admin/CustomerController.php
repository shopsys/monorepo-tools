<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller {

	/**
	 * @Route("/customer/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request, $id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new CustomerFormType());

		try {
			$customerData = array();

			if (!$form->isSubmitted()) {
				$userRepository = $this->get('ss6.shop.customer.user_repository');
				/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */
				$user = $userRepository->getUserById($id);

				$customerData['id'] = $user->getId();
				$customerData['firstName'] = $user->getFirstName();
				$customerData['lastName'] = $user->getLastName();
				$customerData['telephone'] = $user->getBillingAddress()->getTelephone();
				$customerData['email'] = $user->getEmail();
				$customerData['companyName'] = $user->getBillingAddress()->getCompanyName();
				$customerData['companyNumber'] = $user->getBillingAddress()->getCompanyNumber();
				$customerData['companyTaxNumber'] = $user->getBillingAddress()->getCompanyTaxNumber();
				$customerData['street'] = $user->getBillingAddress()->getStreet();
				$customerData['city'] = $user->getBillingAddress()->getCity();
				$customerData['zip'] = $user->getBillingAddress()->getZip();
				$customerData['country'] = $user->getBillingAddress()->getCountry();
				$customerData['deliveryCompanyName'] = $user->getDeliveryAddress()->getCompanyName();
				$customerData['deliveryContactPerson'] = $user->getDeliveryAddress()->getContactPerson();
				$customerData['deliveryTelephone'] = $user->getDeliveryAddress()->getTelephone();
				$customerData['deliveryStreet'] = $user->getDeliveryAddress()->getStreet();
				$customerData['deliveryCity'] = $user->getDeliveryAddress()->getCity();
				$customerData['deliveryZip'] = $user->getDeliveryAddress()->getZip();
				$customerData['deliveryCountry'] = $user->getDeliveryAddress()->getCountry();
			}

			$form->setData($customerData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$customerData = $form->getData();

				$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
				/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */
				$user = $customerEditFacade->edit(
					$id,
					$customerData['firstName'],
					$customerData['lastName'],
					$customerData['email'],
					$customerData['password'],
					$customerData['telephone'],
					$customerData['companyName'],
					$customerData['companyNumber'],
					$customerData['companyTaxNumber'],
					$customerData['street'],
					$customerData['city'],
					$customerData['zip'],
					$customerData['country'],
					$customerData['deliveryCompanyName'],
					$customerData['deliveryContactPerson'],
					$customerData['deliveryTelephone'],
					$customerData['deliveryStreet'],
					$customerData['deliveryCity'],
					$customerData['deliveryZip'],
					$customerData['deliveryCountry']);

				$flashMessage->addSuccess('Byl upraven zákazník ' . $user->getFullName());
				return $this->redirect($this->generateUrl('admin_customer_list'));
			} elseif ($form->isSubmitted()) {
				$user = $this->get('ss6.shop.customer.user_repository')->getUserById($id);
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('Zákazník s tímto emailem již existuje'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Customer/edit.html.twig', array(
			'form' => $form->createView(),
			'user' => $user,
		));
	}

	/**
	 * @Route("/customer/list/")
	 */
	public function listAction() {
		$source = new Entity(User::class);

		$tableAlias = $source->getTableAlias();
		$source->manipulateQuery(function (QueryBuilder $queryBuilder) use ($tableAlias) {
			$queryBuilder
				->addSelect(
					'MAX(CASE WHEN ba.companyName IS NOT NULL
						THEN ba.companyName
						ELSE CONCAT(' . $tableAlias . ".firstName, ' ', " . $tableAlias . '.lastName)
					END) AS name'
				)
				->join($tableAlias.'.billingAddress', 'ba')
				->groupBy($tableAlias);
		});

		$grid = $this->createGrid();
		$grid->setSource($source);

		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'name',
			'type' => 'text',
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'city',
			'type' => 'text',
			'field' => 'billingAddress.city:max',
			'source' => true,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'telephone',
			'type' => 'text',
			'field' => 'billingAddress.telephone:max',
			'source' => true,
		)));

		$grid->setVisibleColumns(array('name', 'city', 'telephone', 'email'));
		$grid->setColumnsOrder(array('name', 'city', 'telephone', 'email'));
		$grid->getColumn('name')->setTitle('Jméno');
		$grid->getColumn('city')->setTitle('Město');
		$grid->getColumn('telephone')->setTitle('Telefon');
		$grid->getColumn('email')->setTitle('Email');
		$grid->setDefaultOrder('name', 'asc');

		return $grid->getGridResponse('@SS6Shop/Admin/Content/Customer/list.html.twig');
	}

	/**
	 * @return \APY\DataGridBundle\Grid\Grid
	 */
	private function createGrid() {
		$grid = $this->get('grid');
		/* @var $grid \APY\DataGridBundle\Grid\Grid */

		$grid->hideFilters();
		$grid->setActionsColumnTitle('Akce');
		$grid->setLimits(array(20));
		$grid->setDefaultLimit(20);

		$detailRowAction = new RowAction('Upravit', 'admin_customer_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$detailRowAction->setAttributes(array('type' => 'edit'));
		$grid->addRowAction($detailRowAction);

		$deleteRowAction = new RowAction('Smazat', 'admin_customer_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete zákazníka smazat?');
		$deleteRowAction->setRouteParameters(array('id'));
		$deleteRowAction->setAttributes(array('type' => 'delete'));
		$grid->addRowAction($deleteRowAction);

		return $grid;
	}

	/**
	 * @Route("/customer/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new CustomerFormType(), null, array(
			'validation_groups' => array('Default', 'create'),
		));

		try {
			$customerData = array();

			$form->setData($customerData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$customerData = $form->getData();
				$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
				/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */

				$user = $customerEditFacade->create(
					$customerData['firstName'],
					$customerData['lastName'],
					$customerData['email'],
					$customerData['password'],
					$customerData['telephone'],
					$customerData['companyName'],
					$customerData['companyNumber'],
					$customerData['companyTaxNumber'],
					$customerData['street'],
					$customerData['city'],
					$customerData['zip'],
					$customerData['country'],
					$customerData['deliveryCompanyName'],
					$customerData['deliveryContactPerson'],
					$customerData['deliveryTelephone'],
					$customerData['deliveryStreet'],
					$customerData['deliveryCity'],
					$customerData['deliveryZip'],
					$customerData['deliveryCountry']);

				$flashMessage->addSuccess('Byl vytvořen zákazník ' . $user->getFullName());
				return $this->redirect($this->generateUrl('admin_customer_list'));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$flashMessage->addError('V databázi se již nachází zákazník s emailem ' . $e->getEmail());
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}

		return $this->render('@SS6Shop/Admin/Content/Customer/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/customer/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$userRepository = $this->get('ss6.shop.customer.user_repository');
		/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */

		try {
			$fullName = $userRepository->getUserById($id)->getFullName();
			$this->get('ss6.shop.customer.customer_edit_facade')->delete($id);

			$flashMessage->addSuccess('Zákazník ' . $fullName . ' byl smazán');
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}

		return $this->redirect($this->generateUrl('admin_customer_list'));
	}

}
