<?php

namespace Shopsys\ShopBundle\Component\FileUpload;

interface EntityFileUploadInterface {

	/**
	 * @return \Shopsys\ShopBundle\Component\FileUpload\FileForUpload[]
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
