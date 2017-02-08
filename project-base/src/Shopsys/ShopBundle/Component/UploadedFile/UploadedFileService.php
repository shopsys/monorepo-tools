<?php

namespace SS6\ShopBundle\Component\UploadedFile;

use SS6\ShopBundle\Component\FileUpload\FileUpload;
use SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use SS6\ShopBundle\Component\UploadedFile\UploadedFile;

class UploadedFileService {

	/**
	 * @var \SS6\ShopBundle\Component\FileUpload\FileUpload
	 */
	private $fileUpload;

	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @param \SS6\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig $uploadedFileEntityConfig
	 * @param int $entityId
	 * @param string[] $temporaryFilenames
	 * @return \SS6\ShopBundle\Component\UploadedFile\UploadedFile
	 */
	public function createUploadedFile(
		UploadedFileEntityConfig $uploadedFileEntityConfig,
		$entityId,
		array $temporaryFilenames
	) {
		$temporaryFilepath = $this->fileUpload->getTemporaryFilePath(array_pop($temporaryFilenames));

		return new UploadedFile(
			$uploadedFileEntityConfig->getEntityName(),
			$entityId,
			pathinfo($temporaryFilepath, PATHINFO_BASENAME)
		);
	}

}
