<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Unit\Image;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Image\ImageViewFacade;
use Shopsys\ReadModelBundle\Image\ImageViewFactory;

class ImageViewFacadeTest extends TestCase
{
    private const IMAGE_EXTENSION = 'jpg';
    private const NOT_EXISTING_PRODUCT_ID = 800;

    /** @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade */
    private $imageFacadeMock;

    protected function setUp()
    {
        parent::setUp();

        $this->imageFacadeMock = $this->createImageFacadeMock();
    }

    public function testGetForEntityIds(): void
    {
        $imageFactory = new ImageViewFactory();
        $imageViewFacade = new ImageViewFacade($this->imageFacadeMock, $imageFactory);

        $imageViews = $imageViewFacade->getForEntityIds('product', [1, 3, 5]);

        $expected = [
            1 => new ImageView(1, self::IMAGE_EXTENSION, 'product', null),
            3 => new ImageView(3, self::IMAGE_EXTENSION, 'product', null),
            5 => new ImageView(5, self::IMAGE_EXTENSION, 'product', null),
        ];

        $this->assertEquals($expected, $imageViews);
    }

    public function testGetForEntityIdsWithNullImages(): void
    {
        $imageFactory = new ImageViewFactory();
        $imageViewFacade = new ImageViewFacade($this->imageFacadeMock, $imageFactory);

        $imageViews = $imageViewFacade->getForEntityIds('product', [10, self::NOT_EXISTING_PRODUCT_ID, 2]);

        $expected = [
            10 => new ImageView(10, self::IMAGE_EXTENSION, 'product', null),
            self::NOT_EXISTING_PRODUCT_ID => null,
            2 => new ImageView(2, self::IMAGE_EXTENSION, 'product', null),
        ];

        $this->assertEquals($expected, $imageViews);
    }

    /**
     * @param int $id
     * @param string $entityClass
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    private function createImageMock(int $id, string $entityClass): Image
    {
        $imageMock = $this->createMock(Image::class);

        $imageMock->method('getId')->willReturn($id);
        $imageMock->method('getExtension')->willReturn(self::IMAGE_EXTENSION);
        $imageMock->method('getEntityName')->willReturn($entityClass);
        $imageMock->method('getType')->willReturn(null);

        return $imageMock;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected function createImageFacadeMock(): ImageFacade
    {
        $imageFacadeMock = $this->createMock(ImageFacade::class);

        $getImageMocksWithSomeNotFoundCallback = function (array $entityIds, string $entityClass): array {
            $images = [];

            foreach ($entityIds as $entityId) {
                if ($entityId === self::NOT_EXISTING_PRODUCT_ID) {
                    continue;
                }

                $images[$entityId] = $this->createImageMock($entityId, $entityClass);
            }

            return $images;
        };

        $imageFacadeMock->method('getImagesByEntitiesIndexedByEntityId')
            ->willReturnCallback($getImageMocksWithSomeNotFoundCallback);

        return $imageFacadeMock;
    }
}
