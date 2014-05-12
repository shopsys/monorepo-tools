<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use Twig_Extension;
use Twig_SimpleFunction;

class FileUploadExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('getNameByCachedFilename', array($this, 'getNameByCachedFilename')),
		);
	}

	/**
	 * @param string $cachedFilename
	 * @return string
	 */
	public function getNameByCachedFilename($cachedFilename) {
		$filename = $this->fileUpload->getOriginFilenameByCached($cachedFilename);
		$filepath = ($this->fileUpload->getCacheDirectory() . DIRECTORY_SEPARATOR . $cachedFilename);
		if (file_exists($filepath) && is_file($filepath) && is_writable($filepath)) {
			$fileSize = round((int)filesize($filepath) / 1024 / 1024, 2);
			return $filename . ' (' . $fileSize . ' MB)';
		}
		return '';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'fileupload_extension';
	}
}
