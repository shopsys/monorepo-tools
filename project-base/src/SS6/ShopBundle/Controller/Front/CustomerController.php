<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller {

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function editAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.front');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		
		if (!$this->get('security.context')->isGranted(Roles::ROLE_CUSTOMER)) {
			$flashMessage->addError('Pro přístup na tuto stránku musíte být přihlášeni');
			return $this->redirect($this->generateUrl('front_login'));
		}

		$user = $this->getUser();

		$form = $this->createForm(new CustomerFormType());

		$customerData = array();

		if (!$form->isSubmitted()) {
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
			$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
			/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */

			$customerData = $form->getData();
			$user = $customerEditFacade->editByCustomer(
				$user->getId(),
				$customerData['firstName'],
				$customerData['lastName'],
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
				$customerData['deliveryCountry']
			);

			$flashMessage->addSuccess('Vaše údaje byly úspěšně zaktualizovány');
			return $this->redirect($this->generateUrl('front_customer_edit'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Front/Content/Customer/edit.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
