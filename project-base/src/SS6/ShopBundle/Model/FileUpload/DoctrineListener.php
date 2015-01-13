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

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->preFlushEntity($entity);
		}
	}

	/**
	 * @param \Doctrine\ORM\Event\PreUpdateEventArgs $args
	 */
	public function preUpdate(PreUpdateEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->preFlushEntity($entity);
		}
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->postFlushEntity($entity);
		}
	}

	/**
	 * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		if ($entity instanceof EntityFileUploadInterface) {
			$this->fileUpload->postFlushEntity($entity);
		}
	}

}
