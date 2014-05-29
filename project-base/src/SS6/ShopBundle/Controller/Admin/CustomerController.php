<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;
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
		$userRepository = $this->get('ss6.shop.customer.user_repository');
		/* @var $userRepository \SS6\ShopBundle\Model\Customer\UserRepository */
		
		$user = $userRepository->getUserById($id);
		$form = $this->createForm(new CustomerFormType());

		try {
			$customerData = array();

			if (!$form->isSubmitted()) {
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
				$customerData['postcode'] = $user->getBillingAddress()->getPostcode();
				$customerData['country'] = $user->getBillingAddress()->getCountry();
				$customerData['deliveryCompanyName'] = $user->getDeliveryAddress()->getCompanyName();
				$customerData['deliveryContactPerson'] = $user->getDeliveryAddress()->getContactPerson();
				$customerData['deliveryTelephone'] = $user->getDeliveryAddress()->getTelephone();
				$customerData['deliveryStreet'] = $user->getDeliveryAddress()->getStreet();
				$customerData['deliveryCity'] = $user->getDeliveryAddress()->getCity();
				$customerData['deliveryPostcode'] = $user->getDeliveryAddress()->getPostcode();
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
					$customerData['postcode'],
					$customerData['country'],
					$customerData['deliveryCompanyName'],
					$customerData['deliveryContactPerson'],
					$customerData['deliveryTelephone'],
					$customerData['deliveryStreet'],
					$customerData['deliveryCity'],
					$customerData['deliveryPostcode'],
					$customerData['deliveryCountry']);

				$flashMessage->addSuccess('Byl upraven zákazník ' . $user->getFullName());
				return $this->redirect($this->generateUrl('admin_customer_list'));
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('V databázi se již nachází zákazník s tímto e-mailem'));
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
	 *
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function listAction() {
		$source = new Entity(User::class);

		$tableAlias = $source->getTableAlias();
		$grid = $this->createGrid();
		$grid->setSource($source);
		$source->manipulateQuery(function (QueryBuilder $queryBuilder) use ($tableAlias, $grid) {
			$queryBuilder
				->addSelect('
					MAX(CASE WHEN ba.companyName IS NOT NULL
						THEN ba.companyName
						ELSE CONCAT(' . $tableAlias . ".firstName, ' ', " . $tableAlias . '.lastName)
					END) AS name,
					(SELECT COUNT(o1.id) FROM ' . Order::class . ' o1 WHERE o1.customer = ' . $tableAlias . '.id) AS orders_count,
					(SELECT SUM(o2.totalPrice) FROM ' . Order::class . ' o2 WHERE o2.customer = ' . $tableAlias . '.id) AS orders_sum_price,
					(SELECT MAX(o3.createdOn) FROM ' . Order::class . ' o3 WHERE o3.customer = ' . $tableAlias . '.id) AS last_order_on'
				)
				->join($tableAlias.'.billingAddress', 'ba')
				->groupBy($tableAlias);
			foreach ($grid->getColumns() as $column) {
				if (!$column->isVisibleForSource() && $column->isSorted()) {
					$queryBuilder->resetDQLPart('orderBy');
					$queryBuilder->orderBy($column->getField(), $column->getOrder());
				}
			}
		});		

		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'name',
			'field' => 'name',
			'source' => false,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'city',
			'field' => 'billingAddress.city:max',
			'source' => true,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'telephone',
			'field' => 'billingAddress.telephone:max',
			'source' => true,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'orders_count',
			'field' => 'orders_count',
			'source' => false,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'orders_sum_price',
			'field' => 'orders_sum_price',
			'source' => false,
		)));
		$grid->getColumns()->addColumn(new TextColumn(array(
			'id' => 'last_order_on',
			'field' => 'last_order_on',
			'source' => false,
		)));

		$grid->setVisibleColumns(array('name', 'city', 'telephone', 'email', 'orders_count', 'orders_sum_price', 'last_order_on'));
		$grid->setColumnsOrder(array('name', 'city', 'telephone', 'email', 'orders_count', 'orders_sum_price', 'last_order_on'));
		$grid->getColumn('name')->setTitle('Jméno');
		$grid->getColumn('city')->setTitle('Město');
		$grid->getColumn('telephone')->setTitle('Telefon');
		$grid->getColumn('email')->setTitle('Email');
		$grid->getColumn('orders_count')->setTitle('Počet objednávek')->setClass('text-right');
		$grid->getColumn('orders_sum_price')->setTitle('Hodnota objednávek')->setClass('text-right');
		$grid->getColumn('last_order_on')->setTitle('Poslední objednávka')->setClass('text-right');
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
					$customerData['postcode'],
					$customerData['country'],
					$customerData['deliveryCompanyName'],
					$customerData['deliveryContactPerson'],
					$customerData['deliveryTelephone'],
					$customerData['deliveryStreet'],
					$customerData['deliveryCity'],
					$customerData['deliveryPostcode'],
					$customerData['deliveryCountry']);

				$flashMessage->addSuccess('Byl vytvořen zákazník ' . $user->getFullName());
				return $this->redirect($this->generateUrl('admin_customer_list'));
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('V databázi se již nachází zákazník s tímto e-mailem'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
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

		$fullName = $userRepository->getUserById($id)->getFullName();
		$this->get('ss6.shop.customer.customer_edit_facade')->delete($id);
		$flashMessage->addSuccess('Zákazník ' . $fullName . ' byl smazán');

		return $this->redirect($this->generateUrl('admin_customer_list'));
	}

}
