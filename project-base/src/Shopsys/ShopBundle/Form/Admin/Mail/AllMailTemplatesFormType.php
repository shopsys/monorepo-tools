<?php

namespace Shopsys\ShopBundle\Form\Admin\Mail;

use Shopsys\ShopBundle\Form\Admin\Mail\MailTemplateFormType;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\ShopBundle\Model\Mail\AllMailTemplatesData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllMailTemplatesFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    public function __construct(ResetPasswordMail $resetPasswordMail)
    {
        $this->resetPasswordMail = $resetPasswordMail;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registrationTemplate', MailTemplateFormType::class)
            ->add('resetPasswordTemplate', MailTemplateFormType::class, [
                'required_subject_variables' => $this->resetPasswordMail->getRequiredSubjectVariables(),
                'required_body_variables' => $this->resetPasswordMail->getRequiredBodyVariables(),
            ])
            ->add('orderStatusTemplates', FormType::COLLECTION, [
                'type' => MailTemplateFormType::class,
            ])
            ->add('domainId', FormType::HIDDEN)
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => AllMailTemplatesData::class,
        ]);
    }
}
