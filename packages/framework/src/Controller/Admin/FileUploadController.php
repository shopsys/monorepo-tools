<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Twig\FileThumbnail\FileThumbnailExtension;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileUploadController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @var \Shopsys\FrameworkBundle\Twig\FileThumbnail\FileThumbnailExtension
     */
    private $fileThumbnailExtension;

    public function __construct(
        FileUpload $fileUpload,
        FileThumbnailExtension $fileThumbnailExtension
    ) {
        $this->fileUpload = $fileUpload;
        $this->fileThumbnailExtension = $fileThumbnailExtension;
    }

    /**
     * @Route("/file-upload/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $actionResult = [
            'status' => 'error',
            'code' => 0,
            'filename' => '',
            'message' => t('Unexpected error occurred, file was not uploaded.'),
        ];
        $file = $request->files->get('file');

        if ($file instanceof UploadedFile) {
            try {
                $temporaryFilename = $this->fileUpload->upload($file);
                $fileThumbnailInfo = $this->fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);

                $actionResult = [
                    'status' => 'success',
                    'filename' => $temporaryFilename,
                    'iconType' => $fileThumbnailInfo->getIconType(),
                    'imageThumbnailUri' => $fileThumbnailInfo->getImageUri(),
                ];
                $actionResult['status'] = 'success';
                $actionResult['filename'] = $temporaryFilename;
            } catch (\Shopsys\FrameworkBundle\Component\FileUpload\Exception\FileUploadException $ex) {
                $actionResult['status'] = 'error';
                $actionResult['code'] = $ex->getCode();
                $actionResult['message'] = $ex->getMessage();
            }
        }

        return new JsonResponse($actionResult);
    }

    /**
     * @Route("/file-upload/delete-temporary-file/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteTemporaryFileAction(Request $request)
    {
        $filename = $request->get('filename');
        $actionResult = $this->fileUpload->tryDeleteTemporaryFile($filename);

        return new JsonResponse($actionResult);
    }
}
