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
			try {
				$cachedFilename = $fileUpload->upload($file);
				$actionResult = array(
					'status' => 'success',
					'filename' => $cachedFilename,
				);
				$actionResult['status'] = 'success';
				$actionResult['filename'] = $cachedFilename;
			} catch (\SS6\ShopBundle\Model\FileUpload\Exception\FileUpload $ex) {
				$actionResult['status'] = 'error';
				$actionResult['code'] = $ex->getCode();
				$actionResult['message'] = $ex->getMessage();
			}
		}

		return new JsonResponse($actionResult);
	}
	
	/**
	 * @Route("/file_upload/delete_cached_file/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function deleteCachedFileAction(Request $request) {
		$fileUpload = $this->get('ss6.shop.file_upload');
		/* @var $fileUpload \SS6\ShopBundle\Model\FileUpload\FileUpload */
		$filename = $request->get('filename');
		$actionResult = $fileUpload->tryDeleteCachedFile($filename);
		
		return new JsonResponse($actionResult);
	}



}
