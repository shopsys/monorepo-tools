<?php

namespace Shopsys\ShopBundle\Form\Front\Newsletter;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\TimedFormTypeExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class SubscriptionFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'newsletter_subscription_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', FormType::EMAIL, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Email(),
                ],
            ])
            ->add('email2', FormType::HONEY_POT)
            ->add('send', FormType::SUBMIT);
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            TimedFormTypeExtension::OPTION_ENABLED => true,
        ]);
    }
}
