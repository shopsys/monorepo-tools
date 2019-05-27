<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Ramsey\Uuid\Uuid;
use Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductQueryParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @experimental
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    protected $productFacade;

    /**
     * @var \Shopsys\BackendApiBundle\Controller\V1\ApiProductTransformer
     */
    protected $productTransformer;

    /**
     * @var \Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer
     */
    protected $linksTransformer;

    /**
     * @var int
     */
    protected $pageSize = 100;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\BackendApiBundle\Controller\V1\ApiProductTransformer $productTransformer
     * @param \Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer $linksTransformer
     */
    public function __construct(ProductFacade $productFacade, ApiProductTransformer $productTransformer, HeaderLinksTransformer $linksTransformer)
    {
        $this->productFacade = $productFacade;
        $this->productTransformer = $productTransformer;
        $this->linksTransformer = $linksTransformer;
    }

    /**
     * Retrieves Product resource
     * @Get("/products/{uuid}")
     * @param string $uuid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProductAction(string $uuid): Response
    {
        $this->validateUuids([$uuid]);

        $product = $this->productFacade->getByUuid($uuid);

        $productArray = $this->productTransformer->transform($product);

        $view = View::create($productArray, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Retrieves multiple Product resources
     * @Get("/products")
     * @QueryParam(name="page", requirements="-?\d+", default=1)
     * @QueryParam(name="uuids", map=true, allowBlank=false)
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getProductsAction(ParamFetcher $paramFetcher, Request $request): Response
    {
        $page = (int)$paramFetcher->get('page');

        $query = new ProductQueryParams($this->pageSize, $page);

        $filterUuids = $paramFetcher->get('uuids');
        if (is_array($filterUuids)) {
            $this->validateUuids($filterUuids);

            $query = $query->withUuids($filterUuids);
        }

        $productsResult = $this->productFacade->findByProductQueryParams($query);

        if ($page > $productsResult->getPage() || $page < 0) {
            throw new UnprocessableEntityHttpException('There are no products on provided page.');
        }

        $productsArray = array_map(function (Product $product) {
            return $this->productTransformer->transform($product);
        }, $productsResult->getResults());

        $links = $this->linksTransformer->fromPaginationResult($productsResult, $request->getUri());

        $view = View::create($productsArray, Response::HTTP_OK, ['Link' => $links->format()]);

        return $this->handleView($view);
    }

    /**
     * @param array $uuids
     */
    protected function validateUuids(array $uuids): void
    {
        $invalidUuids = [];

        foreach ($uuids as $uuid) {
            if (!Uuid::isValid($uuid)) {
                $invalidUuids[] = $uuid;
            }
        }

        if (count($invalidUuids) === 1) {
            throw new BadRequestHttpException('This UUID is not valid: ' . reset($invalidUuids));
        } elseif (count($invalidUuids) > 1) {
            throw new BadRequestHttpException('These UUIDS are not valid: ' . implode(', ', $invalidUuids));
        }
    }
}
