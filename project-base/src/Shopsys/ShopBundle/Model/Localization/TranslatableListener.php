<?php

namespace Shopsys\ShopBundle\Model\Localization;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener as PrezentTranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;

class TranslatableListener extends PrezentTranslatableListener
{
    public function __construct(MetadataFactory $factory) {
        parent::__construct($factory);

        // set default locale to NULL
        // (currentLocale of entities should be set by request or stay NULL)
        $this->setCurrentLocale(null);
    }

    public function getSubscribedEvents() {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
            Events::postPersist,
        ];
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
