<?php

namespace SS6\ShopBundle\Model\FileUpload;

interface EntityFileUploadInterface {

	/**
	 * @return \SS6\ShopBundle\Model\FileUpload\FileForUpload[]
	 */
	public function getCachedFilesForUpload();

	/**
	 * @param string $key
	 * @param string $originFilename
	 */
	public function setFileAsUploaded($key, $originFilename);

	/**
	 * @return int
	 */
	public function getId();
}
