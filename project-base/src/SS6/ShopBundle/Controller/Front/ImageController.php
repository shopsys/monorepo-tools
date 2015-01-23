<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller {

	public function getImageAction($entityName, $type, $size, $imageId) {
		$imageFacade = $this->get('ss6.shop.image.processing.image_generator_facade');
		/* @var $imageFacade \SS6\ShopBundle\Model\Image\Processing\ImageGeneratorFacade */

		$imageFilepath = $imageFacade->generateImageAndGetFilepath($entityName, $imageId, $type, $size);

		return new BinaryFileResponse($imageFilepath);
	}

}
