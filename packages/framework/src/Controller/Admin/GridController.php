<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\InlineEditService;
use Shopsys\FrameworkBundle\Component\Grid\Ordering\GridOrderingFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GridController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\InlineEdit\InlineEditService
     */
    private $inlineEditService;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\Ordering\GridOrderingFacade
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
     * @Route("/_grid/get-form/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function getFormAction(Request $request)
    {
        $renderedFormRow = $this->inlineEditService->getRenderedFormRow(
            $request->get('serviceName'),
            json_decode($request->get('rowId'))
        );

        return new JsonResponse($renderedFormRow);
    }

    /**
     * @Route("/_grid/save-form/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function saveFormAction(Request $request)
    {
        $responseData = [];
        $rowId = json_decode($request->get('rowId'));

        try {
            $rowId = $this->inlineEditService->saveFormData($request->get('serviceName'), $request, $rowId);

            $responseData['success'] = true;
            $responseData['rowHtml'] = $this->inlineEditService->getRenderedRowHtml($request->get('serviceName'), $rowId);
        } catch (\Shopsys\FrameworkBundle\Component\Grid\InlineEdit\Exception\InvalidFormDataException $e) {
            $responseData['success'] = false;
            // reset array keys for array representation in JSON, otherwise it could be treated as an object
            $responseData['errors'] = array_values(array_unique($e->getFormErrors()));
        }

        return new JsonResponse($responseData);
    }

    /**
     * @Route("/_grid/save-ordering/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function saveOrderingAction(Request $request)
    {
        $this->gridOrderingFacade->saveOrdering(
            $request->get('entityClass'),
            array_map('json_decode', $request->get('rowIds'))
        );
        $responseData = ['success' => true];

        return new JsonResponse($responseData);
    }
}
