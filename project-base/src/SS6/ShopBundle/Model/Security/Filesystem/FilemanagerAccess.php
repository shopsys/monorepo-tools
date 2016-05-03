<?php

namespace SS6\ShopBundle\Model\Security\Filesystem;

use FM\ElfinderBundle\Configuration\ElFinderConfigurationReader;

class FilemanagerAccess {

	/**
	 * @var \SS6\ShopBundle\Model\Security\Filesystem\FilemanagerAccess|null
	 */
	private static $self;

	/**
	 * @var string
	 */
	private $filemanagerUploadDir;

	/**
	 * @var \FM\ElfinderBundle\Configuration\ElFinderConfigurationReader
	 */
	private $elFinderConfigurationReader;

	public function __construct(
		$filamanagerUploadDir,
		ElFinderConfigurationReader $elFinderConfigurationReader
	) {
		$this->filemanagerUploadDir = realpath($filamanagerUploadDir);
		$this->elFinderConfigurationReader = $elFinderConfigurationReader;
	}

	/**
	 * @param string $attr
	 * @param string $path
	 * @param $data
	 * @param $volume
	 * @return bool|null
	 */
	public function isPathAccessible($attr, $path, $data, $volume) {
		$realpath = $this->parseClosestRealpath($path);

		if ($realpath === false || strpos($realpath, $this->filemanagerUploadDir) !== 0) {
			return false;
		}

		$pathInUploadDir = substr($realpath, strlen($this->filemanagerUploadDir));
		// must use DIRECTORY_SEPARATOR, because $pathInUploadDir is result of realpath()
		if ($pathInUploadDir !== false && strpos($pathInUploadDir, DIRECTORY_SEPARATOR) !== 0) {
			return false;
		}

		return $this->elFinderConfigurationReader->access($attr, $path, $data, $volume);
	}

	/**
	 * @param string $path
	 * @return string|bool
	 */
	private function parseClosestRealpath($path) {
		if (empty($path)) {
			return false;
		}
		$realpath = realpath($path);

		while ($realpath === false && $path !== dirname($path)) {
			$path = dirname($path);
			$realpath = realpath($path);
		}

		return $realpath;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Security\Filesystem\FilemanagerAccess $filemanagerAccess
	 */
	public static function injectSelf(FilemanagerAccess $filemanagerAccess) {
		self::$self = $filemanagerAccess;
	}

	public static function detachSelf() {
		self::$self = null;
	}

	/**
	 * @param string $attr
	 * @param string $path
	 * @param $data
	 * @param $volume
	 * @return bool|null
	 */
	public static function isPathAccessibleStatic($attr, $path, $data, $volume) {
		if (self::$self === null) {
			throw new \SS6\ShopBundle\Model\Security\Filesystem\Exception\InstanceNotInjectedException();
		}

		return self::$self->isPathAccessible($attr, $path, $data, $volume);
	}
}
