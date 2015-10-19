<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\UploadedFile\Config\FileConfig;
use SS6\ShopBundle\Component\UploadedFile\FileFacade;
use SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension;
use Twig_Extension;
use Twig_SimpleFunction;

class FileExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\Config\FileConfig
	 */
	private $fileConfig;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\FileFacade
	 */
	private $fileFacade;

	/**
	 * @var \SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension
	 */
	private $fileThumbnailExtension;

	public function __construct(
		$fileUrlPrefix,
		Domain $domain,
		FileConfig $fileConfig,
		FileFacade $fileFacade,
		FileThumbnailExtension $fileThumbnailExtension
	) {
		$this->fileUrlPrefix = $fileUrlPrefix;
		$this->domain = $domain;
		$this->fileConfig = $fileConfig;
		$this->fileFacade = $fileFacade;
		$this->fileThumbnailExtension = $fileThumbnailExtension;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('hasFile', [$this, 'hasFile']),
			new Twig_SimpleFunction('fileUrl', [$this, 'getFileUrl']),
			new Twig_SimpleFunction('filePreview', [$this, 'getFilePreviewHtml'], ['is_safe' => ['html']]),
			new Twig_SimpleFunction('getFile', [$this, 'getFileByEntity']),
		];
	}

	/**
	 * @param Object $entity
	 * @return bool
	 */
	public function hasFile($entity) {
		return $this->fileFacade->hasFile($entity);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getFileUrl($entity) {
		$file = $this->getFileByEntity($entity);

		return $this->fileFacade->getFileUrl($this->domain->getCurrentDomainConfig(), $file);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getFilePreviewHtml($entity) {
		$file = $this->getFileByEntity($entity);
		$filepath = $this->fileFacade->getAbsoluteFileFilepath($file);
		$fileThumbnailInfo = $this->fileThumbnailExtension->getFileThumbnailInfo($filepath);

		if ($fileThumbnailInfo->getIconType() !== null) {
			return '<i class="fa fa-' . $fileThumbnailInfo->getIconType() . ' fa-5x"></i>';
		} else {
			return '<img src="' . $fileThumbnailInfo->getImageUri() . '"/>';
		}
	}

	/**
	 * @param Object $entity
	 * @return \SS6\ShopBundle\Component\UploadedFile\File
	 */
	public function getFileByEntity($entity) {
		return $this->fileFacade->getFileByEntity($entity);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'file_extension';
	}
}
