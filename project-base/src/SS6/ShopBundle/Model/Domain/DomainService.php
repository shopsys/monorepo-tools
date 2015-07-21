<?php

namespace SS6\ShopBundle\Model\Domain;

use Intervention\Image\Constraint;
use SS6\ShopBundle\Model\Image\Processing\ImageProcessingService;
use Symfony\Component\Filesystem\Filesystem;

class DomainService {

	const DOMAIN_ICON_WIDTH = 16;
	const DOMAIN_ICON_HEIGHT = 11;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Processing\ImageProcessingService
	 */
	private $imageProcessingService;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	public function __construct(
		ImageProcessingService $imageProcessingService,
		Filesystem $filesystem
	) {
		$this->imageProcessingService = $imageProcessingService;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param int $domainId
	 * @param string $filepath
	 * @param string $domainImagesDirectory
	 */
	public function convertToDomainIconFormatAndSave($domainId, $filepath, $domainImagesDirectory) {
		$newTemporaryFilepath = pathinfo($filepath, PATHINFO_DIRNAME)
			. DIRECTORY_SEPARATOR
			. $domainId
			. '.'
			. ImageProcessingService::EXTENSION_PNG;

		$this->imageProcessingService->createInterventionImage($filepath)
			->fit(self::DOMAIN_ICON_WIDTH, self::DOMAIN_ICON_HEIGHT, function (Constraint $constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})
			->save($newTemporaryFilepath);

		$targetFileName = pathinfo($newTemporaryFilepath, PATHINFO_BASENAME);
		$targetFilePath = $domainImagesDirectory . DIRECTORY_SEPARATOR . $targetFileName;

		try {
			$this->filesystem->rename($newTemporaryFilepath, $targetFilePath, true);
			$this->filesystem->remove($filepath);
		} catch (\Symfony\Component\Filesystem\Exception\IOException $ex) {
			$message = 'Move file from temporary directory to domain directory failed';
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\MoveToFolderFailedException($message, $ex);
		}
	}
}
