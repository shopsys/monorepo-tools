<?php

namespace SS6\ShopBundle\Model\Image;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Image\Image;

class ImageRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getImageRepository() {
		return $this->em->getRepository(Image::class);
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @param string|null $type
	 * @return \SS6\ShopBundle\Model\Image\Image
	 * @throws \SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException
	 */
	public function getImageByEntity($entityName, $entityId, $type) {
		$image = $this->getImageRepository()->findOneBy(array(
			'entityName' => $entityName,
			'entityId' => $entityId,
			'type' => $type
		));

		if ($image === null) {
			$message = 'Image type "' . ($type ?: 'NULL') . '" not found for entity "' . $entityName . '"';
			throw new \SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException($message);
		}

		return $image;
	}
}
