<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Customer\CustomerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends Controller {

	/**
	 * @Route("/customer/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new CustomerFormType());

		try {
			$customerData = array();

			$form->setData($customerData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$customerData = $form->getData();
				$registrationFacade = $this->get('ss6.shop.customer.registration_facade');
				/* @var $registrationFacade \SS6\ShopBundle\Model\Customer\RegistrationFacade */

				$user = $registrationFacade->create(
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
