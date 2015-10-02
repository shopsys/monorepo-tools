<?php

namespace SS6\ShopBundle\Component\FileUpload;

interface EntityFileUploadInterface {

	/**
	 * @return \SS6\ShopBundle\Component\FileUpload\FileForUpload[]
	 */
	public function getTemporaryFilesForUpload();

	/**
	 * @param string $key
	 * @param string $originalFilename
	 */
	public function setFileAsUploaded($key, $originalFilename);

	/**
	 * @return int
	 */
	public function getId();
}
