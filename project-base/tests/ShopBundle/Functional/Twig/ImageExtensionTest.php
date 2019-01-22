<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\AdditionalImageData;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use Shopsys\FrameworkBundle\Twig\ImageExtension;
use Tests\ShopBundle\Test\FunctionalTestCase;

class ImageExtensionTest extends FunctionalTestCase
{
    public function testGetImageHtmlWithAdditional(): void
    {
        $container = $this->getContainer();

        $domain = $container->get(Domain::class);
        $imageLocator = $container->get(ImageLocator::class);
        $templating = $container->get('templating');

        $imageFacade = $this->createMock(ImageFacade::class);

        $image = new Image('product', 2, null, null);

        $imageFacade->method('getImageByObject')->willReturn($image);
        $imageFacade->method('getImageUrl')->willReturn('http://webserver:8080/2.jpg');
        $imageFacade->method('getAdditionalImagesData')->willReturn([
            new AdditionalImageData('(min-width: 1200px)', 'http://webserver:8080/additional_0_2.jpg'),
            new AdditionalImageData('(max-width: 480px)', 'http://webserver:8080/additional_1_2.jpg'),
        ]);

        $imageExtension = new ImageExtension('', $domain, $imageLocator, $imageFacade, $templating);

        $html = $imageExtension->getImageHtml($image);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Resources/picture.twig', $html);

        libxml_clear_errors();
    }
}
