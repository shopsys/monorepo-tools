<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Image\EntityImageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;
use Twig_Extension;
use Twig_SimpleFunction;

class ImageExtension extends Twig_Extension {

	/**
	 * @var \Symfony\Component\Templating\Helper\CoreAssetsHelper
	 */
	private $assetsHelper;

	/**
	 * @var string
	 */
	private $imageUrlPrefix;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param string $imageUrlPrefix
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper $assetsHelper
	 */
	function __construct($imageUrlPrefix, ContainerInterface $container, CoreAssetsHelper $assetsHelper) {
		$this->imageUrlPrefix = $imageUrlPrefix;
		$this->assetsHelper = $assetsHelper;
		$this->container = $container; // Must inject main container - https://github.com/symfony/symfony/issues/2347
	}

	/**
	 * Get service "templating" cannot by called in constructor - https://github.com/symfony/symfony/issues/2347
	 *
	 * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
	 */
	private function getTemplatingService() {
		return $this->container->get('templating');
	}

		/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('getImageUrl', array($this, 'getImageUrl')),
			new Twig_SimpleFunction('image', array($this, 'getImageHtml'), array('is_safe' => array('html'))),
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\EntityImageInterface $entity
	 * @param string|null $type
	 * @return string
	 */
	public function getImageUrl(EntityImageInterface $entity, $type = null) {
		$imageFileCollection = $entity->getImageFileCollection();
		return $this->assetsHelper->getUrl($this->imageUrlPrefix . $imageFileCollection->getRelativUrlImage($type));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\EntityImageInterface $entity
	 * @param string|null $type
	 * @param array $attr
	 * @return string
	 */
	public function getImageHtml(EntityImageInterface $entity, $type = null, $attr = array()) {
		$imageFileCollection = $entity->getImageFileCollection();
		$imageFile = $imageFileCollection->getImageFile($type);
		/* @var $imageFile \SS6\ShopBundle\Model\Image\ImageFile */ // NetBeans Intellisense Bug
		$attr['src'] = $this->getImageUrl($entity, $type);
		return $this->getTemplatingService()->render('@SS6Shop/Common/image.html.twig', array(
			'attr' => $attr,
			'category' => $imageFileCollection->getCategory(),
			'type' => $type,
			'title' => $imageFile->getTitle(),
		));
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'image_extension';
	}
}
