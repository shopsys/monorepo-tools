<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\Grid\InlineEdit\InlineEditService;
use SS6\ShopBundle\Model\Grid\Ordering\GridOrderingFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GridController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Grid\InlineEdit\InlineEditService
	 */
	private $inlineEditService;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\Ordering\GridOrderingFacade
	 */
	private $gridOrderingFacade;

	public function __construct(
		GridOrderingFacade $gridOrderingFacade,
		InlineEditService $inlineEditService
	) {
		$this->gridOrderingFacade = $gridOrderingFacade;
		$this->inlineEditService = $inlineEditService;
	}

	/**
	 * @Route("/_grid/get_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function getFormAction(Request $request) {
		$renderedFormRow = $this->inlineEditService->getRenderedFormRow(
			$request->get('serviceName'),
			json_decode($request->get('rowId'))
		);

		return new JsonResponse($renderedFormRow);
	}

	/**
	 * @Route("/_grid/save_form/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveFormAction(Request $request) {
		$responseData = [];
		$rowId = json_decode($request->get('rowId'));

		try {
			$rowId = $this->inlineEditService->saveFormData($request->get('serviceName'), $request, $rowId);
			$responseData['success'] = true;
			$responseData['rowHtml'] = $this->inlineEditService->getRenderedRowHtml($request->get('serviceName'), $rowId);
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
		$this->transactional(
			function () use ($request) {
				$this->gridOrderingFacade->saveOrdering(
					$request->get('entityClass'),
					array_map('json_decode', $request->get('rowIds'))
				);
			}
		);
		$responseData = ['success' => true];

		return new JsonResponse($responseData);
	}

}
