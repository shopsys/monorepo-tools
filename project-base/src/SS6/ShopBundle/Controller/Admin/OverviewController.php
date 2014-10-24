<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OverviewController extends Controller {

	/**
	 * @Route("/overview/")
	 */
	public function listAction() {
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$domainConfigs = $domain->getAll();

		return $this->render('@SS6Shop/Admin/Content/Overview/list.html.twig', array(
			'domainConfigs' => $domainConfigs,
		));
	}
}
