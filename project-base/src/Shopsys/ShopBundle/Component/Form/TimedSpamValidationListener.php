<?php

namespace Shopsys\ShopBundle\Component\Form;

use Shopsys\ShopBundle\Component\Form\FormTimeProvider;
use Shopsys\ShopBundle\Form\TimedFormTypeExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TimedSpamValidationListener implements EventSubscriberInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Form\FormTimeProvider
     */
    private $formTimeProvider;

    /**
     * @var string[]
     */
    private $options;

    /**
     * @param \Shopsys\ShopBundle\Component\Form\FormTimeProvider $formTimeProvider
     * @param array $options
     */
    public function __construct(FormTimeProvider $formTimeProvider, array $options)
    {
        $this->formTimeProvider = $formTimeProvider;
        $this->options = $options;
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        if ($form->isRoot() &&
            $form->getConfig()->getOption('compound') &&
            !$this->formTimeProvider->isFormTimeValid($form->getName(), $this->options)
        ) {
            $message = tc(
                '{1} You must wait 1 second before submitting the form.
                |[2,Inf] You must wait %seconds% seconds before submitting the form.',
                $this->options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS],
                [
                    '%seconds%' => $this->options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS],
                ]
            );
            $form->addError(new FormError($message));
        }
        $this->formTimeProvider->removeFormTime($form->getName());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }
}
