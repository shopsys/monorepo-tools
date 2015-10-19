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
		return $this->uploadedFileFacade->hasFile($entity);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getFileUrl($entity) {
		$file = $this->getFileByEntity($entity);

		return $this->uploadedFileFacade->getFileUrl($this->domain->getCurrentDomainConfig(), $file);
	}

	/**
	 * @param Object $entity
	 * @return string
	 */
	public function getFilePreviewHtml($entity) {
		$file = $this->getFileByEntity($entity);
		$filepath = $this->uploadedFileFacade->getAbsoluteFileFilepath($file);
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
		return $this->uploadedFileFacade->getFileByEntity($entity);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'file_extension';
	}
}
