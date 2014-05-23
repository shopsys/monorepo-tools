<?php

namespace SS6\ShopBundle\Model\FileUpload;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineListener {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(\SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	public function prePersist(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->preFlushEntity($entity);
		}
	}

	public function preUpdate(PreUpdateEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->preFlushEntity($entity);
		}
	}

	public function postPersist(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->postFlushEntity($entity);
		}
	}

	public function postUpdate(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->postFlushEntity($entity);
		}
	}

}
