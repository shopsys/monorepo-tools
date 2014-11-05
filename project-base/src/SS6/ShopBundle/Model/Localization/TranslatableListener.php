<?php

namespace SS6\ShopBundle\Model\Localization;

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener as PrezentTranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;

class TranslatableListener extends PrezentTranslatableListener {

	public function getSubscribedEvents() {
		return array(
			Events::loadClassMetadata,
			Events::postLoad,
			Events::postPersist,
		);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$entity = $args->getEntity();
		$metadata = $this->getTranslatableMetadata(get_class($entity));
		if ($metadata instanceof TranslatableMetadata) {
			if ($metadata->fallbackLocale) {
				$metadata->fallbackLocale->setValue($entity, $this->getFallbackLocale());
			}

			if ($metadata->currentLocale) {
				$metadata->currentLocale->setValue($entity, $this->getCurrentLocale());
			}
		}
	}

}