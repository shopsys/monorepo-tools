<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Unit\Image;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Image\ImageViewFactory;

class ImageViewFactoryTest extends TestCase
{
    public function testCreateFromImage(): void
    {
        $imageMock = $this->createMock(Image::class);
        $imageMock->method('getId')->willReturn(1);
        $imageMock->method('getExtension')->willReturn('jpg');
        $imageMock->method('getEntityName')->willReturn('product');
        $imageMock->method('getType')->willReturn(null);

        $imageFactory = new ImageViewFactory();

        $imageView = $imageFactory->createFromImage($imageMock);

        $this->assertEquals(new ImageView(1, 'jpg', 'product', null), $imageView);
    }
}
