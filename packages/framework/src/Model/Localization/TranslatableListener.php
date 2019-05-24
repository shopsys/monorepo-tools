<?php

namespace Shopsys\FrameworkBundle\Model\Localization;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Metadata\MetadataFactory;
use Prezent\Doctrine\Translatable\EventListener\TranslatableListener as PrezentTranslatableListener;
use Prezent\Doctrine\Translatable\Mapping\TranslatableMetadata;

class TranslatableListener extends PrezentTranslatableListener
{
    /**
     * @param \Metadata\MetadataFactory $factory
     */
    public function __construct(MetadataFactory $factory)
    {
        parent::__construct($factory);

        // set default locale to NULL
        // (currentLocale of entities should be set by request or stay NULL)
        $this->setCurrentLocale(null);
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
            Events::postPersist,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $metadata = $this->getTranslatableMetadata(get_class($entity));
        if ($metadata instanceof TranslatableMetadata) {
            /** @var \Prezent\Doctrine\Translatable\Mapping\PropertyMetadata|null $fallbackLocale */
            $fallbackLocale = $metadata->fallbackLocale;
            if ($fallbackLocale !== null) {
                $metadata->fallbackLocale->setValue($entity, $this->getFallbackLocale());
            }

            /** @var \Prezent\Doctrine\Translatable\Mapping\PropertyMetadata|null $currentLocale */
            $currentLocale = $metadata->currentLocale;
            if ($currentLocale !== null) {
                $metadata->currentLocale->setValue($entity, $this->getCurrentLocale());
            }
        }
    }
}
