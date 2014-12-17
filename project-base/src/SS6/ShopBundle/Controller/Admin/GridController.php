<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GridController extends Controller {

	/**
	 * @Route("/_grid/get_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function getFormAction(Request $request) {
		$inlineEditService = $this->get('ss6.shop.grid.inline_edit.inline_edit_service');
		/* @var $inlineEditService \SS6\ShopBundle\Model\Grid\InlineEdit\InlineEditService */

		$renderedFormWidgets = $inlineEditService->getRenderedFormWidgets(
			$request->get('serviceName'),
			$request->get('rowId')
		);

		return new JsonResponse($renderedFormWidgets);
	}

	/**
	 * @Route("/_grid/save_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveFormAction(Request $request) {
		$inlineEditService = $this->get('ss6.shop.grid.inline_edit.inline_edit_service');
		/* @var $inlineEditService \SS6\ShopBundle\Model\Grid\InlineEdit\InlineEditService */

		$responseData = [];
		$rowId = $request->get('rowId');

		try {
			$rowId = $inlineEditService->saveFormData($request->get('serviceName'), $request, $rowId);
			$responseData['success'] = true;
			$responseData['rowHtml'] = $inlineEditService->getRenderedRowHtml($request->get('serviceName'), $rowId);
		} catch (\SS6\ShopBundle\Model\Grid\InlineEdit\Exception\InvalidFormDataException $e) {
			$responseData['success'] = false;
			$responseData['errors'] = array_unique($e->getFormErrors());
		}

		return new JsonResponse($responseData);
	}

	/**
	 * @Route("/_grid/save_ordering/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveOrderingAction(Request $request) {
		$gridOrderingFacade = $this->get('ss6.shop.grid.ordering.grid_ordering_facade');
		/* @var $gridOrderingFacade \SS6\ShopBundle\Model\Grid\Ordering\GridOrderingFacade */

		$gridOrderingFacade->saveOrdering($request->get('entityClass'), $request->get('rowIds'));
		$responseData = array('success' => true);

		return new JsonResponse($responseData);
	}

}
