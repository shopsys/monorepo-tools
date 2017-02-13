<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use Shopsys\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension;
use Twig_Extension;
use Twig_SimpleFunction;

class UploadedFileExtension extends Twig_Extension
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade
     */
    private $uploadedFileFacade;

    /**
     * @var \Shopsys\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension
     */
    private $fileThumbnailExtension;

    public function __construct(
        Domain $domain,
        UploadedFileFacade $uploadedFileFacade,
        FileThumbnailExtension $fileThumbnailExtension
    ) {
        $this->domain = $domain;
        $this->uploadedFileFacade = $uploadedFileFacade;
        $this->fileThumbnailExtension = $fileThumbnailExtension;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('hasUploadedFile', [$this, 'hasUploadedFile']),
            new Twig_SimpleFunction('uploadedFileUrl', [$this, 'getUploadedFileUrl']),
            new Twig_SimpleFunction('uploadedFilePreview', [$this, 'getUploadedFilePreviewHtml'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('getUploadedFile', [$this, 'getUploadedFileByEntity']),
        ];
    }

    /**
     * @param Object $entity
     * @return bool
     */
    public function hasUploadedFile($entity)
    {
        return $this->uploadedFileFacade->hasUploadedFile($entity);
    }

    /**
     * @param Object $entity
     * @return string
     */
    public function getUploadedFileUrl($entity)
    {
        $uploadedFile = $this->getUploadedFileByEntity($entity);

        return $this->uploadedFileFacade->getUploadedFileUrl($this->domain->getCurrentDomainConfig(), $uploadedFile);
    }

    /**
     * @param Object $entity
     * @return string
     */
    public function getUploadedFilePreviewHtml($entity)
    {
        $uploadedFile = $this->getUploadedFileByEntity($entity);
        $filepath = $this->uploadedFileFacade->getAbsoluteUploadedFileFilepath($uploadedFile);
        $fileThumbnailInfo = $this->fileThumbnailExtension->getFileThumbnailInfo($filepath);

        if ($fileThumbnailInfo->getIconType() !== null) {
            $classes = [
                'svg',
                'svg-file-' . $fileThumbnailInfo->getIconType(),
                'list-images__item__image__type',
                'list-images__item__image__type--' . $fileThumbnailInfo->getIconType(),
                'text-no-decoration',
                'cursor-pointer',
            ];

            return '<i class="' . implode(' ', $classes) . '"></i>';
        } else {
            return '<img src="' . $fileThumbnailInfo->getImageUri() . '"/>';
        }
    }

    /**
     * @param Object $entity
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFile
     */
    public function getUploadedFileByEntity($entity)
    {
        return $this->uploadedFileFacade->getUploadedFileByEntity($entity);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'file_extension';
    }
}
