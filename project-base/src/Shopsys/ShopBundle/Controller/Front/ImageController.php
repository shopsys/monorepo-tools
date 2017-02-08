<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Image\Config\ImageConfig;
use SS6\ShopBundle\Component\Image\Processing\ImageGeneratorFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Image\Processing\ImageGeneratorFacade
	 */
	private $imageGeneratorFacade;

	public function __construct(ImageGeneratorFacade $imageGeneratorFacade) {
		$this->imageGeneratorFacade = $imageGeneratorFacade;
	}

	public function getImageAction($entityName, $type, $sizeName, $imageId) {
		if ($sizeName === ImageConfig::DEFAULT_SIZE_NAME) {
			$sizeName = null;
		}

		try {
			$imageFilepath = $this->imageGeneratorFacade->generateImageAndGetFilepath($entityName, $imageId, $type, $sizeName);
		} catch (\SS6\ShopBundle\Component\Image\Exception\ImageException $e) {
			$message = 'Generate image for entity "' . $entityName
				. '" (type=' . $type . ', size=' . $sizeName . ', imageId=' . $imageId . ') failed.';
			throw $this->createNotFoundException($message, $e);
		}

		try {
			return new BinaryFileResponse($imageFilepath);
		} catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
			$message = 'Response with file "' . $imageFilepath . '" failed.';
			throw $this->createNotFoundException($message, $e);
		}
	}

}
