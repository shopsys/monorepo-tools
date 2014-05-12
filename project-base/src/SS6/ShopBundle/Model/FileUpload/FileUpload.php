<?php

namespace SS6\ShopBundle\Model\FileUpload;

use SS6\ShopBundle\Model\String\TransformString;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload {

	const CACHE_DIRECTORY = 'fileUploads';

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @param string $cacheDir
	 */
	public function __construct($cacheDir) {
		$this->cacheDir = $cacheDir;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @throws \SS6\ShopBundle\Model\FileUpload\Exception\UploadFailedException
	 */
	public function upload(UploadedFile $file) {
		if ($file->getError()) {
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\UploadFailedException($file->getErrorMessage(), $file->getError());
		}

		$cacheFilename = $this->getCacheFilename($file->getClientOriginalName());
		$file->move($this->getCacheDirectory(), $cacheFilename);

		return $cacheFilename;
	}

	/**
	 * @param string $filename
	 * @return boolean
	 */
	public function tryDeleteCachedFile($filename) {
		$directory = $this->getCacheDirectory();
		$filepath = $directory . DIRECTORY_SEPARATOR . TransformString::safeFilename($filename);
		if (file_exists($filepath) && is_file($filepath) && is_writable($filepath)) {
			unlink($filepath);
			return true;
		}
		return false;
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	private function getCacheFilename($filename) {
		return TransformString::safeFilename(uniqid() . '__' . $filename);
	}

	/**
	 * @return string
	 */
	public function getCacheDirectory() {
		return $this->cacheDir . DIRECTORY_SEPARATOR . self::CACHE_DIRECTORY;
	}

	/**
	 * @param string $cachedFilename
	 * @return string
	 */
	public function getOriginFilenameByCached($cachedFilename) {
		$matches = array();
		if ($cachedFilename && preg_match('/^.+?__(.+)$/', $cachedFilename, $matches)) {
			return $matches[1];
		}
		return '';
	}
	
}
