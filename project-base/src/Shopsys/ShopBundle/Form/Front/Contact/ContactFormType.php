<?php

namespace Shopsys\ShopBundle\Form\Front\Contact;

use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\TimedFormTypeExtension;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ContactFormType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'contact_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter full name']),
                ],
            ])
            ->add('message', FormType::TEXTAREA, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter content']),
                ],
            ])
            ->add('email', FormType::EMAIL, [
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter e-mail']),
                    new Email(['message' => 'Please enter valid e-mail']),
                ],
            ])
            ->add('email2', FormType::HONEY_POT)
            ->add('send', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactFormData::class,
            'attr' => ['novalidate' => 'novalidate'],
            TimedFormTypeExtension::OPTION_ENABLED => true,
        ]);
    }
}
