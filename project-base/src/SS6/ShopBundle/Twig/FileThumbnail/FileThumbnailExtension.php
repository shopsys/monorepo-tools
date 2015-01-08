<?php

namespace SS6\ShopBundle\Twig\FileThumbnail;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Image\Processing\ImageEditor;
use SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailInfo;
use Twig_Extension;
use Twig_SimpleFunction;

class FileThumbnailExtension extends Twig_Extension {

	const DEFAULT_ICON_TYPE = 'file-o';
	const IMAGE_THUMBNAIL_QUALITY = 50;

	/**
	 * @var string[]
	 */
	private $iconsByExtension;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Processing\ImageEditor
	 */
	private $imageEditor;

	public function __construct(FileUpload $fileUpload, ImageEditor $imageEditor) {
		$this->fileUpload = $fileUpload;
		$this->imageEditor = $imageEditor;
		$this->iconsByExtension = [
			'csv' => 'file-excel-o',
			'doc' => 'file-word-o',
			'docx' => 'file-word-o',
			'ods' => 'file-excel-o',
			'odt' => 'file-word-o',
			'pdf' => 'file-pdf-o',
			'txt' => 'file-text-o',
			'xls' => 'file-excel-o',
			'xlsx' => 'file-excel-o',
			'xml' => 'file-code-o',
		];
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('getFileThumbnailInfoByTemporaryFilename', array($this, 'getFileThumbnailInfoByTemporaryFilename')),
		);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'file_thumbnail_extension';
	}

	/**
	 * @param string $temporaryFilename
	 * @return \SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailInfo
	 */
	public function getFileThumbnailInfoByTemporaryFilename($temporaryFilename) {
		try {
			return $this->getImageThumbnailInfo($temporaryFilename);
		} catch (\SS6\ShopBundle\Model\Image\Processing\Exception\FileIsNotSupportedImageException $ex) {
			return new FileThumbnailInfo($this->getIconTypeByFilename($temporaryFilename));
		}
	}

	/**
	 * @param string $temporaryFilename
	 * @return FileThumbnailInfo
	 */
	private function getImageThumbnailInfo($temporaryFilename) {
		$image = $this->imageEditor->getImageThumbnail($this->fileUpload->getTemporaryFilepath($temporaryFilename));
		
		return new FileThumbnailInfo(null, $image->encode('data-url', self::IMAGE_THUMBNAIL_QUALITY)->getEncoded());
	}

	/**
	 * @param string $filename
	 * @return string
	 */
	private function getIconTypeByFilename($filename) {
		$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if (array_key_exists($extension, $this->iconsByExtension)) {
			return $this->iconsByExtension[$extension];
		}

		return self::DEFAULT_ICON_TYPE;
	}

}
