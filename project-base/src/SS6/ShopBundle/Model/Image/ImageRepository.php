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
	 * @return \SS6\ShopBundle\Model\Image\Image|null
	 */
	public function findImageByEntity($entityName, $entityId, $type) {
		$image = $this->getImageRepository()->findOneBy(array(
				'entityName' => $entityName,
				'entityId' => $entityId,
				'type' => $type
			),
			array('id' => 'asc')
		);

		return $image;
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @param string|null $type
	 * @return \SS6\ShopBundle\Model\Image\Image
	 */
	public function getImageByEntity($entityName, $entityId, $type) {
		$image = $this->findImageByEntity($entityName, $entityId, $type);
		if ($image === null) {
			$message = 'Image of type "' . ($type ?: 'NULL') . '" not found for entity "' . $entityName . '" with ID ' . $entityId;
			throw new \SS6\ShopBundle\Model\Image\Exception\ImageNotFoundException($message);
		}

		return $image;
	}

	/**
	 * @param string $entityName
	 * @param int $entityId
	 * @param string|null $type
	 * @return \SS6\ShopBundle\Model\Image\Image[]
	 */
	public function getImagesByEntity($entityName, $entityId, $type) {
		return $this->getImageRepository()->findBy(array(
				'entityName' => $entityName,
				'entityId' => $entityId,
				'type' => $type
			),
			array('id' => 'asc')
		);
	}
}
