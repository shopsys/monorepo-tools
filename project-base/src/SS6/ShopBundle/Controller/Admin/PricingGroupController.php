<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Pricing\Group\PricingGroupSettingsFormType;
use SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PricingGroupController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit
	 */
	private $pricingGroupInlineEdit;

	/**
	 * @var \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	public function __construct(
		Translator $translator,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		EntityManager $em,
		PricingGroupFacade $pricingGroupFacade,
		PricingGroupInlineEdit $pricingGroupInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
	) {
		$this->translator = $translator;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->em = $em;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->pricingGroupInlineEdit = $pricingGroupInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
	}

	/**
	 * @Route("/pricing/group/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$grid = $this->pricingGroupInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/pricing/group/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');
		$newId = $newId !== null ? (int)$newId : null;

		try {
			$name = $this->pricingGroupFacade->getById($id)->getName();
			$this->em->transactional(
				function () use ($id, $newId) {
					$this->pricingGroupFacade->delete($id, $newId);
				}
			);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig('Cenová skupina <strong>{{ name }}</strong> byla smazána', [
					'name' => $name,
				]);
			} else {
				$newPricingGroup = $this->pricingGroupFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					'Cenová skupina <strong>{{ name }}</strong> byla smazána a byla nahrazena skupinou'
					. ' <strong>{{ newName }}</strong>.',
					[
						'name' => $name,
						'newName' => $newPricingGroup->getName(),
					]);
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená cenová skupina neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_pricinggroup_list'));
	}

	/**
	 * @Route("/pricing/group/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$pricingGroup = $this->pricingGroupFacade->getById($id);
			$pricingGroupsNamesById = [];
			$pricingGroups = $this->pricingGroupFacade->getAllExceptIdByDomainId($id, $pricingGroup->getDomainId());
			foreach ($pricingGroups as $newPricingGroup) {
				$pricingGroupsNamesById[$newPricingGroup->getId()] = $newPricingGroup->getName();
			}
			if ($this->pricingGroupSettingFacade->isPricingGroupUsed($pricingGroup)) {
				$message = $this->translator->trans(
					'Pro odstranění cenové skupiny "%name%" musíte zvolit, která se má všude, '
					. 'kde je aktuálně používaná, nastavit.' . "\n\n" . 'Jakou cenovou skupinu místo ní chcete nastavit?',
					['%name%' => $pricingGroup->getName()]
				);

				if ($this->pricingGroupSettingFacade->isPricingGroupDefault($pricingGroup)) {
					$message = $this->translator->trans(
						'Cenová skupina "%name%" je nastavena jako výchozí. '
						. 'Pro její odstranění musíte zvolit, která se má všude, '
						. 'kde je aktuálně používaná, nastavit.' . "\n\n" . 'Jakou cenovou skupinu místo ní chcete nastavit?',
						['%name%' => $pricingGroup->getName()]
					);
				}

				return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_pricinggroup_delete',
					$id,
					$pricingGroupsNamesById
				);
			} else {
				$message = $this->translator->trans(
					'Opravdu si přejete trvale odstranit cenovou skupinu "%name%"? Nikde není použita.',
					['%name%' => $pricingGroup->getName()]
				);
				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_pricinggroup_delete', $id);
			}

		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			return new Response($this->translator->trans('Zvolená cenová skupina neexistuje.'));
		}

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$pricingGroups = $this->pricingGroupSettingFacade->getPricingGroupsBySelectedDomainId();
		$form = $this->createForm(new PricingGroupSettingsFormType($pricingGroups));

		$pricingGroupSettingsFormData = [];
		$pricingGroupSettingsFormData['defaultPricingGroup'] = $this->pricingGroupSettingFacade
			->getDefaultPricingGroupBySelectedDomain();

		$form->setData($pricingGroupSettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingGroupSettingsFormData = $form->getData();
			$this->pricingGroupSettingFacade->setDefaultPricingGroup($pricingGroupSettingsFormData['defaultPricingGroup']);
			$this->getFlashMessageSender()->addSuccessFlash('Nastavení výchozí cenové skupiny bylo upraveno');

			return $this->redirect($this->generateUrl('admin_pricinggroup_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/pricingGroupSettings.html.twig', [
			'form' => $form->createView(),
		]);

	}
}
