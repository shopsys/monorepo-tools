<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DomainController extends Controller {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		Domain $domain,
		SelectedDomain $selectedDomain,
		Localization $localization
	) {
		$this->domain = $domain;
		$this->selectedDomain = $selectedDomain;
		$this->localization = $localization;
	}

	public function domainTabsAction() {
		return $this->render('@SS6Shop/Admin/Inline/Domain/tabs.html.twig', [
			'domainConfigs' => $this->domain->getAll(),
			'selectedDomainId' => $this->selectedDomain->getId(),
			'multipleLocales' => count($this->localization->getAllLocales()) > 1,
		]);
	}

	/**
	 * @Route("/multidomain/select_domain/{id}", requirements={"id" = "\d+"})
	 * @param Request $request
	 */
	public function selectDomainAction(Request $request, $id) {
		$id = (int)$id;

		$this->selectedDomain->setId($id);

		$referer = $request->server->get('HTTP_REFERER');
		if ($referer === null) {
			return $this->redirect($this->generateUrl('admin_dashboard'));
		} else {
			return $this->redirect($referer);
		}
	}

}
