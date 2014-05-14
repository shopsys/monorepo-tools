<?php

namespace SS6\ShopBundle\Model\FileUpload;

class FileNamingConvention {

	const TYPE_ID = 1;
	const TYPE_ORIGIN_NAME = 2;

	/**
	 * @param int $naminConventionType
	 * @param string $originFilename
	 * @param int|null $entityId
	 * @return string
	 */
	public function getFilenameByNamingConvention($naminConventionType, $originFilename, $entityId = null) {
		if ($naminConventionType === self::TYPE_ID && is_int($entityId)) {
			return $entityId . '.' . pathinfo($originFilename, PATHINFO_EXTENSION);
		} elseif ($naminConventionType === self::TYPE_ORIGIN_NAME) {
			return $originFilename;
		} else {
			$message = 'Naming convention ' . $naminConventionType . ' cannot by resolved to filename';
			throw new \SS6\ShopBundle\Model\FileUpload\Exception\UnresolvedNamingConventionException($message);
		}
	}
}
