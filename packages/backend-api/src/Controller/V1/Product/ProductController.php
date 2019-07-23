<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
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
     * @var \Shopsys\BackendApiBundle\Controller\V1\Product\ApiProductTransformer
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
     * @var \Shopsys\BackendApiBundle\Controller\V1\Product\ProductDataFactoryInterface
     */
    protected $productDataFactory;

    /**
     * @var \Shopsys\BackendApiBundle\Controller\V1\Product\ProductApiDataValidatorInterface
     */
    protected $productApiDataValidator;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\BackendApiBundle\Controller\V1\Product\ApiProductTransformer $productTransformer
     * @param \Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer $linksTransformer
     * @param \Shopsys\BackendApiBundle\Controller\V1\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\BackendApiBundle\Controller\V1\Product\ProductApiDataValidatorInterface $productApiDataValidator
     */
    public function __construct(
        ProductFacade $productFacade,
        ApiProductTransformer $productTransformer,
        HeaderLinksTransformer $linksTransformer,
        ProductDataFactoryInterface $productDataFactory,
        ProductApiDataValidatorInterface $productApiDataValidator
    ) {
        $this->productFacade = $productFacade;
        $this->productTransformer = $productTransformer;
        $this->linksTransformer = $linksTransformer;
        $this->productDataFactory = $productDataFactory;
        $this->productApiDataValidator = $productApiDataValidator;
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
     * Create a Product resource
     * If UUID ins't specified, generates it's own
     * @Post("/products")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProductAction(Request $request): Response
    {
        $productApiData = $request->request->all();
        $uuid = $productApiData['uuid'] ?? null;
        if ($uuid) {
            $this->validateCreatingProductWithDefinedUuid((string)$uuid);
        }

        $errors = $this->productApiDataValidator->validateCreate($productApiData);

        if (count($errors) > 0) {
            return $this->handleView($this->createValidationView($errors));
        }

        $productData = $this->productDataFactory->createFromApi($productApiData, $uuid);
        $this->productFacade->create($productData);

        $location = sprintf('%s/%s', $request->getUri(), $productData->uuid);
        $view = View::create([], Response::HTTP_CREATED, ['Location' => $location]);

        return $this->handleView($view);
    }

    /**
     * Delete a Product resource
     * @Delete("/products/{uuid}")
     * @param string $uuid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteProductAction(string $uuid): Response
    {
        $this->validateUuids([$uuid]);

        $product = $this->productFacade->getByUuid($uuid);
        $this->assertProductIsNotVariantType($product);

        $this->productFacade->delete($product->getId());

        $view = View::create([], Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }

    /**
     * @param array $errors
     * @return \FOS\RestBundle\View\View
     */
    protected function createValidationView(array $errors): View
    {
        $code = Response::HTTP_BAD_REQUEST;
        $message = 'Provided data did not pass validation';

        return View::create(['code' => $code, 'message' => $message, 'errors' => $errors], $code);
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

    /**
     * @param string $uuid
     */
    protected function validateCreatingProductWithDefinedUuid(string $uuid): void
    {
        $this->validateUuids([$uuid]);
        try {
            $this->productFacade->getByUuid($uuid);

            throw new UnprocessableEntityHttpException('Product with ' . $uuid . ' UUID already exists');
        } catch (\Shopsys\FrameworkBundle\Model\Product\Exception\ProductNotFoundException $e) {
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    protected function assertProductIsNotVariantType(Product $product): void
    {
        if ($product->isVariant() || $product->isMainVariant()) {
            throw new BadRequestHttpException('cannot update/delete variant/main variant, this functionality is not supported yet');
        }
    }

    /**
     * Partially update a Product resource
     * @Patch("/products/{uuid}")
     * @param string $uuid
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patchProductAction(string $uuid, Request $request): Response
    {
        $this->validateUuids([$uuid]);
        $productApiData = $request->request->all();
        $errors = $this->productApiDataValidator->validateUpdate($productApiData);
        if (count($errors)) {
            return $this->handleView($this->createValidationView($errors));
        }

        $product = $this->productFacade->getByUuid($uuid);
        $this->assertProductIsNotVariantType($product);

        $productData = $this->productDataFactory->createFromProductAndApi($product, $productApiData);

        $this->productFacade->edit($product->getId(), $productData);

        $view = View::create([], Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }
}
