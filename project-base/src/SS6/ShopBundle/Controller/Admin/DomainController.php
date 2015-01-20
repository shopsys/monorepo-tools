<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DomainController extends Controller {

	public function domainTabsAction() {
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */
		$localization = $this->get('ss6.shop.localization.localization');
		/* @var $localization \SS6\ShopBundle\Model\Localization\Localization */

		return $this->render('@SS6Shop/Admin/Inline/Domain/tabs.html.twig', [
			'domainConfigs' => $domain->getAll(),
			'selectedDomainId' => $selectedDomain->getId(),
			'multipleLocales' => count($localization->getAllLocales()) > 1,
		]);
	}

	/**
	 * @Route("/multidomain/select_domain/{id}", requirements={"id" = "\d+"})
	 * @param Request $request
	 */
	public function selectDomainAction(Request $request, $id) {
		$id = (int)$id;
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$selectedDomain->setId($id);

		$referer = $request->server->get('HTTP_REFERER');
		if ($referer === null) {
			return $this->redirect($this->generateUrl('admin_dashboard'));
		} else {
			return $this->redirect($referer);
		}
	}

}
