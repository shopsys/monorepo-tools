<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Form\Front\Customer\CustomerFormType;
use SS6\ShopBundle\Model\Country\CountryFacade;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Security\LoginAsUserFacade;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerFacade
	 */
	private $customerFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginAsUserFacade
	 */
	private $loginAsUserFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	public function __construct(
		CustomerFacade $customerFacade,
		OrderFacade $orderFacade,
		Domain $domain,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		LoginAsUserFacade $loginAsUserFacade,
		CountryFacade $countryFacade
	) {
		$this->customerFacade = $customerFacade;
		$this->orderFacade = $orderFacade;
		$this->domain = $domain;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->loginAsUserFacade = $loginAsUserFacade;
		$this->countryFacade = $countryFacade;
	}

	public function editAction(Request $request) {
		if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
			$this->getFlashMessageSender()->addErrorFlash(t('Pro přístup na tuto stránku musíte být přihlášeni'));
			return $this->redirectToRoute('front_login');
		}

		$user = $this->getUser();

		$form = $this->createForm(new CustomerFormType($this->countryFacade->getAllOnCurrentDomain()));

		$customerData = new CustomerData();
		$customerData->setFromEntity($user);

		$form->setData($customerData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$customerData = $form->getData();

			$this->customerFacade->editByCustomer($user->getId(), $customerData);

			$this->getFlashMessageSender()->addSuccessFlash(t('Vaše údaje byly úspěšně zaktualizovány'));
			return $this->redirectToRoute('front_customer_edit');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Front/Content/Customer/edit.html.twig', [
			'form' => $form->createView(),
		]);
	}

	public function ordersAction() {
		if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
			$this->getFlashMessageSender()->addErrorFlash(t('Pro přístup na tuto stránku musíte být přihlášeni'));
			return $this->redirectToRoute('front_login');
		}

		$user = $this->getUser();
		/* @var $user \SS6\ShopBundle\Model\Customer\User */

		$orders = $this->orderFacade->getCustomerOrderList($user);
		return $this->render('@SS6Shop/Front/Content/Customer/orders.html.twig', [
			'orders' => $orders,
		]);
	}

	/**
	 * @param string $orderNumber
	 */
	public function orderDetailRegisteredAction($orderNumber) {
		return $this->orderDetailAction(null, $orderNumber);
	}

	/**
	 * @param string $urlHash
	 */
	public function orderDetailUnregisteredAction($urlHash) {
		return $this->orderDetailAction($urlHash, null);
	}

	/**
	 * @param string $urlHash
	 * @param string $orderNumber
	 */
	private function orderDetailAction($urlHash = null, $orderNumber = null) {
		if ($orderNumber !== null) {
			if (!$this->isGranted(Roles::ROLE_CUSTOMER)) {
				$this->getFlashMessageSender()->addErrorFlash(t('Pro přístup na tuto stránku musíte být přihlášeni'));
				return $this->redirectToRoute('front_login');
			}

			$user = $this->getUser();
			try {
				$order = $this->orderFacade->getByOrderNumberAndUser($orderNumber, $user);
				/* @var $order \SS6\ShopBundle\Model\Order\Order */
			} catch (\SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException $ex) {
				$this->getFlashMessageSender()->addErrorFlash(t('Objednávka nebyla nalezena'));
				return $this->redirectToRoute('front_customer_orders');
			}
		} else {
			$order = $this->orderFacade->getByUrlHashAndDomain($urlHash, $this->domain->getId());
			/* @var $order \SS6\ShopBundle\Model\Order\Order */
		}

		$orderItemTotalPricesById = $this->orderItemPriceCalculation->calculateTotalPricesIndexedById($order->getItems());

		return $this->render('@SS6Shop/Front/Content/Customer/orderDetail.html.twig', [
			'order' => $order,
			'orderItemTotalPricesById' => $orderItemTotalPricesById,
		]);

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAsRememberedUserAction(Request $request) {
		try {
			$this->loginAsUserFacade->loginAsRememberedUser($request);
		} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundException $e) {
			$adminFlashMessageSender = $this->get('ss6.shop.component.flash_message.sender.admin');
			/* @var $adminFlashMessageSender \SS6\ShopBundle\Component\FlashMessage\FlashMessageSender */
			$adminFlashMessageSender->addErrorFlash(t('Uživatel nebyl nalezen.'));

			return $this->redirectToRoute('admin_customer_list');
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException $e) {
			throw $this->createAccessDeniedException('', $e);
		}

		return $this->redirectToRoute('front_homepage');
	}

}
