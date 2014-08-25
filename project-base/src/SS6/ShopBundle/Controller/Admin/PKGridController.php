<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PKGridController extends Controller {

	/**
	 * @Route("/_pkgrid/get_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function getFormAction(Request $request) {
		$inlineEditService = $this->get('ss6.shop.pkgrid.inline_edit.inline_edit_service');
		/* @var $inlineEditService \SS6\ShopBundle\Model\PKGrid\InlineEdit\InlineEditService */

		$formData = $inlineEditService->getFormData(
			$request->get('serviceName'),
			$request->get('rowId')
		);

		return new JsonResponse($formData);
	}

	/**
	 * @Route("/_pkgrid/save_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveFormAction(Request $request) {
		$inlineEditService = $this->get('ss6.shop.pkgrid.inline_edit.inline_edit_service');
		/* @var $inlineEditService \SS6\ShopBundle\Model\PKGrid\InlineEdit\InlineEditService */

		$formData = $inlineEditService->saveFormData(
			$request->get('serviceName'),
			$request,
			$request->get('rowId')
		);

		return new JsonResponse($formData);
	}
	
}
