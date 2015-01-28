<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller {

	public function getImageAction($entityName, $type, $sizeName, $imageId) {
		$imageGeneratorFacade = $this->get('ss6.shop.image.processing.image_generator_facade');
		/* @var $imageGeneratorFacade \SS6\ShopBundle\Model\Image\Processing\ImageGeneratorFacade */

		if ($sizeName === ImageConfig::DEFAULT_SIZE_NAME) {
			$sizeName = null;
		}

		try {
			$imageFilepath = $imageGeneratorFacade->generateImageAndGetFilepath($entityName, $imageId, $type, $sizeName);
		} catch (\SS6\ShopBundle\Model\Image\Exception\ImageException $e) {
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
