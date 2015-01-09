<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FileUploadController extends Controller {

	/**
	 * @Route("/file_upload/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function uploadAction(Request $request) {
		$actionResult = array(
			'status' => 'error',
			'code' => 0,
			'filename' => '',
			'message' => 'Došlo k neočekávané chybě, soubor nebyl nahrán.',
		);
		$file = $request->files->get('file');

		if ($file instanceof UploadedFile) {
			$fileUpload = $this->get('ss6.shop.file_upload');
			/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
			$fileThumbnailExtension = $this->get('ss6.shop.file.file_thumbnail_extension');
			/* @var $fileThumbnailExtension \SS6\ShopBundle\Twig\FileThumbnail\FileThumbnailExtension */

			try {
				$temporaryFilename = $fileUpload->upload($file);
				$fileThumbnailInfo = $fileThumbnailExtension->getFileThumbnailInfoByTemporaryFilename($temporaryFilename);
				
				$actionResult = array(
					'status' => 'success',
					'filename' => $temporaryFilename,
					'iconType' => $fileThumbnailInfo->getIconType(),
					'imageThumbnailUri' => $fileThumbnailInfo->getImageUri(),
				);
				$actionResult['status'] = 'success';
				$actionResult['filename'] = $temporaryFilename;
			} catch (\SS6\ShopBundle\Model\FileUpload\Exception\FileUpload $ex) {
				$actionResult['status'] = 'error';
				$actionResult['code'] = $ex->getCode();
				$actionResult['message'] = $ex->getMessage();
			}
		}

		return new JsonResponse($actionResult);
	}

	/**
	 * @Route("/file_upload/delete_temporary_file/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function deleteTemporaryFileAction(Request $request) {
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$filename = $request->get('filename');
		$actionResult = $fileUpload->tryDeleteTemporaryFile($filename);

		return new JsonResponse($actionResult);
	}

}
