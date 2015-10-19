<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension;
use Twig_Extension;
use Twig_SimpleFunction;

class FileExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\UploadedFile\UploadedFileFacade
	 */
	private $uploadedFileFacade;

	/**
	 * @var \SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension
	 */
	private $fileThumbnailExtension;

	public function __construct(
		$fileUrlPrefix,
		Domain $domain,
		UploadedFileFacade $uploadedFileFacade,
		FileThumbnailExtension $fileThumbnailExtension
	) {
		$this->fileUrlPrefix = $fileUrlPrefix;
		$this->domain = $domain;
		$this->uploadedFileFacade = $uploadedFileFacade;
		$this->fileThumbnailExtension = $fileThumbnailExtension;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('hasUploadedFile', [$this, 'hasUploadedFile']),
			new Twig_SimpleFunction('fileUrl', [$this, 'getUploadedFileUrl']),
			new Twig_SimpleFunction('filePreview', [$this, 'getFilePreviewHtml'], ['is_safe' => ['html']]),
			new Twig_SimpleFunction('getFile', [$this, 'getUploadedFileByEntity']),
		];
	}

	/**
	 * @param Object $entity
	 * @return bool
	 */
	public function hasUploadedFile($entity) {
		return $this->uploadedFileFacade->hasUploadedFile($entity);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getUploadedFileUrl($entity) {
		$uploadedFile = $this->getUploadedFileByEntity($entity);

		return $this->uploadedFileFacade->getUploadedFileUrl($this->domain->getCurrentDomainConfig(), $uploadedFile);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getFilePreviewHtml($entity) {
		$uploadedFile = $this->getUploadedFileByEntity($entity);
		$filepath = $this->uploadedFileFacade->getAbsoluteUploadedFileFilepath($uploadedFile);
		$fileThumbnailInfo = $this->fileThumbnailExtension->getFileThumbnailInfo($filepath);

		if ($fileThumbnailInfo->getIconType() !== null) {
			return '<i class="fa fa-' . $fileThumbnailInfo->getIconType() . ' fa-5x"></i>';
		} else {
			return '<img src="' . $fileThumbnailInfo->getImageUri() . '"/>';
		}
	}

	/**
	 * @param Object $entity
	 * @return \SS6\ShopBundle\Component\UploadedFile\UploadedFile
	 */
	public function getUploadedFileByEntity($entity) {
		return $this->uploadedFileFacade->getUploadedFileByEntity($entity);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'file_extension';
	}
}
