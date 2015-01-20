<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Pricing\Currency\CurrencyDomainSettingsFormType;
use SS6\ShopBundle\Form\Admin\Pricing\Currency\CurrencySettingsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller {

	/**
	 * @Route("/currency/list/")
	 */
	public function listAction() {
		$currencyInlineEdit = $this->get('ss6.shop.pricing.currency.currency_inline_edit');
		/* @var $currencyInlineEdit \SS6\ShopBundle\Model\Pricing\Currency\CurrencyInlineEdit */

		$grid = $currencyInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Currency/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/currency/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */
		$confirmDeleteResponseFactory = $this->get('ss6.shop.confirm_delete.confirm_delete_response_factory');
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */;

		try {
			$currency = $currencyFacade->getById($id);
			$message = 'Opravdu si přejete trvale odstranit měnu "' . $currency->getName() . '"?';

			return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_currency_delete', $id);
		} catch (\SS6\ShopBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException $ex) {
			return new Response('Zvolená měna neexistuje.');
		}

	}

	/**
	 * @Route("/currency/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */

		try {
			$fullName = $currencyFacade->getById($id)->getName();

			$currencyFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Měna <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Pricing\Currency\Exception\DeletingNotAllowedToDeleteCurrencyException $ex) {
			$flashMessageSender->addErrorFlash('Tuto měnu nelze smazat, je nastavena jako výchozí nebo je uložena u objednávky');
		} catch (\SS6\ShopBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená měna neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_currency_list'));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$currencies = $currencyFacade->getAll();
		$form = $this->createForm(new CurrencySettingsFormType($currencies));

		$currencySettingsFormData = [];
		$currencySettingsFormData['defaultCurrency'] =  $currencyFacade->getDefaultCurrency();
		$form->setData($currencySettingsFormData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$currencySettingsFormData = $form->getData();
			$currencyFacade->setDefaultCurrency($currencySettingsFormData['defaultCurrency']);
			$flashMessageSender->addSuccessFlashTwig('Nastavení výchozí měny bylo upraveno');

			return $this->redirect($this->generateUrl('admin_currency_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Currency/currencySettings.html.twig', [
			'form' => $form->createView(),
		]);

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function domainSettingsAction(Request $request) {
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$currencies = $currencyFacade->getAll();
		$form = $this->createForm(new CurrencyDomainSettingsFormType($currencies));

		$formData = [];
		$domainNames = [];
		$defaultCurrencies = [];

		foreach ($domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			$defaultCurrencies[$domainId] = $currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
			$domainNames[$domainId] = $domainConfig->getDomain();
		}

		$formData['domainDefaultCurrencies'] = $defaultCurrencies;
		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$defaultCurrencies = $form->getData();
			foreach ($domain->getAll() as $domainConfig) {
				$domainId = $domainConfig->getId();
				$currencyFacade->setDomainDefaultCurrency(
					$defaultCurrencies['domainDefaultCurrencies'][$domainId],
					$domainId
				);
			}
			$flashMessageSender->addSuccessFlash('Nastavení základních měn pro každou doménu bylo upraveno');

			return $this->redirect($this->generateUrl('admin_currency_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Currency/domainCurrencySettings.html.twig', [
			'form' => $form->createView(),
			'domainNames' => $domainNames,
		]);
	}

}
