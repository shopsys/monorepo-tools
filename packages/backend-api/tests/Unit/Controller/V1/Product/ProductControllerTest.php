<?php

declare(strict_types=1);

namespace Tests\BackendApiBundle\Unit\Controller\V1\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\BackendApiBundle\Component\HeaderLinks\HeaderLinksTransformer;
use Shopsys\BackendApiBundle\Controller\V1\Product\ApiProductTransformer;
use Shopsys\BackendApiBundle\Controller\V1\Product\ProductController;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @experimental
 */
class ProductControllerTest extends TestCase
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $productFacade;

    /**
     * @var \Shopsys\BackendApiBundle\Controller\V1\Product\ProductController
     */
    protected $productController;

    protected function setUp()
    {
        $productTransformer = new ApiProductTransformer($this->createDomain());
        $this->productFacade = $this->createMock(ProductFacade::class);
        $linksTransformer = new HeaderLinksTransformer();

        $this->productController = new ProductController($this->productFacade, $productTransformer, $linksTransformer);
    }

    public function testGetProductActionWithUuidIncludingInvalidCharacter()
    {
        $this->expectException(BadRequestHttpException::class);

        $this->productController->getProductAction('09be9850-9a3a-443f-b993-4c1230467b3x');
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected function createDomain(): Domain
    {
        return new Domain(
            [new DomainConfig(1, 'http://example.com/', 'czech', 'cs')],
            $this->createMock(Setting::class)
        );
    }
}
