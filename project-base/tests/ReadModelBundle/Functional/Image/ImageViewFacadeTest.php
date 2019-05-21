<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Functional\Image;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Image\ImageViewFacade;
use Tests\ShopBundle\Test\FunctionalTestCase;

class ImageViewFacadeTest extends FunctionalTestCase
{
    private const PRODUCT_ID_1 = 1;
    private const PRODUCT_ID_2 = 2;
    private const PRODUCT_ID_3 = 3;

    private const PRODUCT_IMAGE_PAIRS = [
        self::PRODUCT_ID_1 => 1,
        self::PRODUCT_ID_2 => 2,
        self::PRODUCT_ID_3 => 3,
    ];

    private const INVALID_PRODUCT_ID = 99999;

    public function testGetForSingleEntityId(): void
    {
        $imageViewFacade = $this->getContainer()->get(ImageViewFacade::class);

        $imageViews = $imageViewFacade->getForEntityIds(Product::class, [self::PRODUCT_ID_1]);

        $expected = [
            self::PRODUCT_ID_1 => new ImageView(self::PRODUCT_IMAGE_PAIRS[self::PRODUCT_ID_1], 'jpg', 'product', null),
        ];

        $this->assertEquals($expected, $imageViews);
    }

    public function testGetForInvalidEntityId(): void
    {
        $imageViewFacade = $this->getContainer()->get(ImageViewFacade::class);

        $imageViews = $imageViewFacade->getForEntityIds(Product::class, [self::INVALID_PRODUCT_ID]);

        $expected = [
            self::INVALID_PRODUCT_ID => null,
        ];

        $this->assertEquals($expected, $imageViews);
    }

    public function testGetForEntityIds(): void
    {
        $imageViewFacade = $this->getContainer()->get(ImageViewFacade::class);

        $imageViews = $imageViewFacade->getForEntityIds(Product::class, [self::PRODUCT_ID_1, self::PRODUCT_ID_2, self::PRODUCT_ID_3]);

        $expected = [
            self::PRODUCT_ID_1 => new ImageView(self::PRODUCT_IMAGE_PAIRS[self::PRODUCT_ID_1], 'jpg', 'product', null),
            self::PRODUCT_ID_2 => new ImageView(self::PRODUCT_IMAGE_PAIRS[self::PRODUCT_ID_2], 'jpg', 'product', null),
            self::PRODUCT_ID_3 => new ImageView(self::PRODUCT_IMAGE_PAIRS[self::PRODUCT_ID_3], 'jpg', 'product', null),
        ];

        $this->assertEquals($expected, $imageViews);
    }
}
