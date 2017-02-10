<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Form\ResizeFormListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener as SymfonyResizerFormListener;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Make CollectionType use custom ResizeFormListener
 */
class CollectionType extends AbstractTypeExtension
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->removeOriginalResizeFormListener($builder->getEventDispatcher());

        $resizeListener = new ResizeFormListener(
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * @return string
     */
    public function getExtendedType() {
        return 'collection';
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    private function removeOriginalResizeFormListener(EventDispatcherInterface $eventDispatcher) {
        $listenersByEventName = $eventDispatcher->getListeners();

        foreach ($listenersByEventName as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                if (isset($listener[0])) {
                    if ($listener[0] instanceof SymfonyResizerFormListener) {
                        $eventDispatcher->removeListener($eventName, $listener);
                    }
                }
            }
        }
    }
}
