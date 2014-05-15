<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
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
				return $this->redirect($this->generateUrl('admin_default_dashboard'));
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
				return $this->redirect($this->generateUrl('admin_default_dashboard'));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			throw $this->createNotFoundException($e->getMessage(), $e);
		}

		return $this->render('@SS6Shop/Admin/Content/Customer/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
