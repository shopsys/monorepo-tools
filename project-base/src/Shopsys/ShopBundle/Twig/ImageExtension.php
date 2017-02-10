<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Image\ImageLocator;
use Shopsys\ShopBundle\Component\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension {

	const NOIMAGE_FILENAME = 'noimage.jpg';

	/**
	 * @var string
	 */
	private $frontDesignImageUrlPrefix;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Component\Image\ImageLocator
	 */
	private $imageLocator;

	/**
	 * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
	 */
	private $imageConfig;

	/**
	 * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @param string $frontDesignImageUrlPrefix
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
	 * @param \Shopsys\ShopBundle\Component\Image\ImageLocator $imageLocator
	 * @param \Shopsys\ShopBundle\Component\Image\Config\ImageConfig $imageConfig
	 * @param \Shopsys\ShopBundle\Component\Image\ImageFacade $imageFacade
	 */
	public function __construct(
		$frontDesignImageUrlPrefix,
		ContainerInterface $container,
		Domain $domain,
		ImageLocator $imageLocator,
		ImageConfig $imageConfig,
		ImageFacade $imageFacade
	) {
		$this->frontDesignImageUrlPrefix = $frontDesignImageUrlPrefix;
		$this->container = $container;
		$this->domain = $domain;
		$this->imageLocator = $imageLocator;
		$this->imageConfig = $imageConfig;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * Get service "templating" cannot be called in constructor - https://github.com/symfony/symfony/issues/2347
	 * because it causes circular dependency
	 *
	 * @return \Symfony\Bundle\TwigBundle\TwigEngine
	 */
	private function getTemplatingService() {
		return $this->container->get('templating');
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('imageExists', [$this, 'imageExists']),
			new Twig_SimpleFunction('imageUrl', [$this, 'getImageUrl']),
			new Twig_SimpleFunction('image', [$this, 'getImageHtml'], ['is_safe' => ['html']]),
			new Twig_SimpleFunction('noimage', [$this, 'getNoimageHtml'], ['is_safe' => ['html']]),
			new Twig_SimpleFunction('getImages', [$this, 'getImages']),
		];
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Image\Image|Object $imageOrEntity
	 * @param string|null $type
	 * @return bool
	 */
	public function imageExists($imageOrEntity, $type = null) {
		try {
			$image = $this->imageFacade->getImageByObject($imageOrEntity, $type);
		} catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
			return false;
		}

		return $this->imageLocator->imageExists($image);
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Image\Image|Object $imageOrEntity
	 * @param string|null $sizeName
	 * @param string|null $type
	 * @return string
	 */
	public function getImageUrl($imageOrEntity, $sizeName = null, $type = null) {
		try {
			return $this->imageFacade->getImageUrl($this->domain->getCurrentDomainConfig(), $imageOrEntity, $sizeName, $type);
		} catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
			return $this->getEmptyImageUrl();
		}
	}

	/**
	 * @param Object $entity
	 * @param string|null $type
	 * @return \Shopsys\ShopBundle\Component\Image\Image[]
	 */
	public function getImages($entity, $type = null) {
		return $this->imageFacade->getImagesByEntityIndexedById($entity, $type);
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Image\Image|Object $imageOrEntity
	 * @param array $attributes
	 * @return string
	 */
	public function getImageHtml($imageOrEntity, array $attributes = []) {
		$this->preventDefault($attributes);

		try {
			$image = $this->imageFacade->getImageByObject($imageOrEntity, $attributes['type']);
			$entityName = $image->getEntityName();
			$attributes['src'] = $this->getImageUrl($image, $attributes['size'], $attributes['type']);
		} catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
			$entityName = 'noimage';
			$attributes['src'] = $this->getEmptyImageUrl();
		}

		return $this->getImageHtmlByEntityName($attributes, $entityName);
	}

	/**
	 * @param array $attributes
	 * @return string
	 */
	public function getNoimageHtml(array $attributes = []) {
		$this->preventDefault($attributes);

		$entityName = 'noimage';
		$attributes['src'] = $this->getEmptyImageUrl();

		return $this->getImageHtmlByEntityName($attributes, $entityName);
	}

	/**
	 * @return string
	 */
	private function getEmptyImageUrl() {
		return $this->domain->getUrl() . $this->frontDesignImageUrlPrefix . self::NOIMAGE_FILENAME;
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Image\Image $image
	 * @param string|null $sizeName
	 * @return string
	 */
	private function getImageCssClass($entityName, $type, $sizeName) {
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
	public function getName() {
		return 'image_extension';
	}

	/**
	 * @param array $attributes
	 */
	private function preventDefault(array &$attributes) {
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
	private function getImageHtmlByEntityName(array $attributes, $entityName) {
		$htmlAttributes = $attributes;
		unset($htmlAttributes['type'], $htmlAttributes['size']);

		return $this->getTemplatingService()->render('@ShopsysShop/Common/image.html.twig', [
			'attr' => $htmlAttributes,
			'imageCssClass' => $this->getImageCssClass($entityName, $attributes['type'], $attributes['size']),
		]);
	}
}
