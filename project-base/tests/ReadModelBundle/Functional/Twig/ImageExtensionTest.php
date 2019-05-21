<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Functional\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\AdditionalImageData;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Twig\ImageExtension;
use Tests\ShopBundle\Test\FunctionalTestCase;

class ImageExtensionTest extends FunctionalTestCase
{
    public function testGetImageHtmlWithMockedImageFacade(): void
    {
        $productId = 2;
        $entityName = 'product';
        $fileExtension = 'jpg';

        $imageFacadeMock = $this->createMock(ImageFacade::class);
        $imageFacadeMock->method('getImageUrlFromAttributes')->willReturn(sprintf('http://webserver:8080/%s/%d.%s', $entityName, $productId, $fileExtension));
        $imageFacadeMock->method('getAdditionalImagesDataFromAttributes')->willReturn([
            new AdditionalImageData('(min-width: 1200px)', sprintf('http://webserver:8080/%s/additional_0_%d.%s', $entityName, $productId, $fileExtension)),
            new AdditionalImageData('(max-width: 480px)', sprintf('http://webserver:8080/%s/additional_1_%d.%s', $entityName, $productId, $fileExtension)),
        ]);

        $imageView = new ImageView($productId, $fileExtension, $entityName, null);

        $readModelBundleImageExtension = $this->createImageExtension('', $imageFacadeMock);
        $html = $readModelBundleImageExtension->getImageHtml($imageView);

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/Resources/picture.twig', $html);

        libxml_clear_errors();
    }

    public function testGetImageHtml(): void
    {
        $productId = 1;
        $entityName = 'product';
        $fileExtension = 'jpg';

        $imageView = new ImageView($productId, $fileExtension, $entityName, null);

        $readModelBundleImageExtension = $this->createImageExtension();
        $html = $readModelBundleImageExtension->getImageHtml($imageView);

        $expected = '<picture>';
        $expected .= sprintf('    <source media="(min-width: 480px) and (max-width: 768px)" srcset="%s/content-test/images/product/default/additional_0_1.jpg"/>', $this->getCurrentUrl());
        $expected .= sprintf('    <img alt="" class="image-product" itemprop="image" src="%s/content-test/images/product/default/1.jpg" title=""/>', $this->getCurrentUrl());
        $expected .= '</picture>';

        $this->assertXmlStringEqualsXmlString($expected, $html);

        libxml_clear_errors();
    }

    public function testGetNoImageHtml(): void
    {
        $readModelBundleImageExtension = $this->createImageExtension();

        $html = $readModelBundleImageExtension->getImageHtml(null);

        $expected = '<picture>';
        $expected .= sprintf('    <img alt="" class="image-noimage" title=""  itemprop="image" src="%s/noimage.png"/>', $this->getCurrentUrl());
        $expected .= '</picture>';

        $this->assertXmlStringEqualsXmlString($expected, $html);

        libxml_clear_errors();
    }

    public function testGetNoImageHtmlWithDefaultFrontDesignImageUrlPrefix(): void
    {
        $defaultFrontDesignImageUrlPrefix = '/assets/frontend/images/';

        $readModelBundleImageExtension = $this->createImageExtension($defaultFrontDesignImageUrlPrefix);
        $html = $readModelBundleImageExtension->getImageHtml(null);

        $expected = '<picture>';
        $expected .= sprintf('    <img alt="" class="image-noimage" title=""  itemprop="image" src="%s%snoimage.png"/>', $this->getCurrentUrl(), $defaultFrontDesignImageUrlPrefix);
        $expected .= '</picture>';

        $this->assertXmlStringEqualsXmlString($expected, $html);

        libxml_clear_errors();
    }

    /**
     * @return string
     */
    private function getCurrentUrl(): string
    {
        $domain = $this->getContainer()->get(Domain::class);

        return $domain->getCurrentDomainConfig()->getUrl();
    }

    /**
     * @param string $frontDesignImageUrlPrefix
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade|null $imageFacade
     * @return \Shopsys\ReadModelBundle\Twig\ImageExtension
     */
    private function createImageExtension(string $frontDesignImageUrlPrefix = '', ?ImageFacade $imageFacade = null): ImageExtension
    {
        $imageLocator = $this->getContainer()->get(ImageLocator::class);
        $templating = $this->getContainer()->get('templating');
        $domain = $this->getContainer()->get(Domain::class);
        $imageFacade = $imageFacade ?: $this->getContainer()->get(ImageFacade::class);

        return new ImageExtension($frontDesignImageUrlPrefix, $domain, $imageLocator, $imageFacade, $templating);
    }
}
