<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Image\ImageLocator;
use Shopsys\FrameworkBundle\Component\Utils\Utils;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension
{
    const NOIMAGE_FILENAME = 'noimage.png';

    /**
     * @var string
     */
    private $frontDesignImageUrlPrefix;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templating;

    /**
     * @param string $frontDesignImageUrlPrefix
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageLocator $imageLocator
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     */
    public function __construct(
        $frontDesignImageUrlPrefix,
        Domain $domain,
        ImageLocator $imageLocator,
        ImageFacade $imageFacade,
        EngineInterface $templating
    ) {
        $this->frontDesignImageUrlPrefix = $frontDesignImageUrlPrefix;
        $this->domain = $domain;
        $this->imageLocator = $imageLocator;
        $this->imageFacade = $imageFacade;
        $this->templating = $templating;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('imageExists', [$this, 'imageExists']),
            new Twig_SimpleFunction('imageUrl', [$this, 'getImageUrl']),
            new Twig_SimpleFunction('image', [$this, 'getImageHtml'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('noimage', [$this, 'getNoimageHtml'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('getImages', [$this, 'getImages']),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image|object $imageOrEntity
     * @param string|null $type
     * @return bool
     */
    public function imageExists($imageOrEntity, $type = null)
    {
        try {
            $image = $this->imageFacade->getImageByObject($imageOrEntity, $type);
        } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException $e) {
            return false;
        }

        return $this->imageLocator->imageExists($image);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image|Object $imageOrEntity
     * @param string|null $sizeName
     * @param string|null $type
     * @return string
     */
    public function getImageUrl($imageOrEntity, $sizeName = null, $type = null)
    {
        try {
            return $this->imageFacade->getImageUrl($this->domain->getCurrentDomainConfig(), $imageOrEntity, $sizeName, $type);
        } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException $e) {
            return $this->getEmptyImageUrl();
        }
    }

    /**
     * @param Object $entity
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getImages($entity, $type = null)
    {
        return $this->imageFacade->getImagesByEntityIndexedById($entity, $type);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image|Object $imageOrEntity
     * @param array $attributes
     * @return string
     */
    public function getImageHtml($imageOrEntity, array $attributes = [])
    {
        $this->preventDefault($attributes);

        try {
            $image = $this->imageFacade->getImageByObject($imageOrEntity, $attributes['type']);
            $entityName = $image->getEntityName();
            $attributes['src'] = $this->getImageUrl($image, $attributes['size'], $attributes['type']);
        } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException $e) {
            $entityName = 'noimage';
            $attributes['src'] = $this->getEmptyImageUrl();
        }

        return $this->getImageHtmlByEntityName($attributes, $entityName);
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function getNoimageHtml(array $attributes = [])
    {
        $this->preventDefault($attributes);

        $entityName = 'noimage';
        $attributes['src'] = $this->getEmptyImageUrl();

        return $this->getImageHtmlByEntityName($attributes, $entityName);
    }

    /**
     * @return string
     */
    private function getEmptyImageUrl()
    {
        return $this->domain->getUrl() . $this->frontDesignImageUrlPrefix . self::NOIMAGE_FILENAME;
    }

    /**
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     * @return string
     */
    private function getImageCssClass($entityName, $type, $sizeName)
    {
        $allClassParts = [
            'image',
            $entityName,
            $type,
            $sizeName,
        ];
        $classParts = array_filter($allClassParts);

        return implode('-', $classParts);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image_extension';
    }

    /**
     * @param array $attributes
     */
    private function preventDefault(array &$attributes)
    {
        Utils::setArrayDefaultValue($attributes, 'type');
        Utils::setArrayDefaultValue($attributes, 'size');
        Utils::setArrayDefaultValue($attributes, 'alt', '');
        Utils::setArrayDefaultValue($attributes, 'title', $attributes['alt']);
    }

    /**
     * @param array $attributes
     * @param $entityName
     * @return string
     */
    private function getImageHtmlByEntityName(array $attributes, $entityName)
    {
        $htmlAttributes = $attributes;
        unset($htmlAttributes['type'], $htmlAttributes['size']);

        return $this->templating->render('@ShopsysFramework/Common/image.html.twig', [
            'attr' => $htmlAttributes,
            'imageCssClass' => $this->getImageCssClass($entityName, $attributes['type'], $attributes['size']),
        ]);
    }
}
