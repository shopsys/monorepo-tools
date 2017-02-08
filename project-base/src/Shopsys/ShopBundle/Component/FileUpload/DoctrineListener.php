<?php

namespace Shopsys\ShopBundle\Component\FileUpload;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;

class DoctrineListener {

	/**
	 * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \Shopsys\ShopBundle\Component\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
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
