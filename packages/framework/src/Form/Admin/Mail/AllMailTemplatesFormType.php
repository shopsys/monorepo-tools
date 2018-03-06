<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Mail;

use Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\FrameworkBundle\Model\Mail\AllMailTemplatesData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllMailTemplatesFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail
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
            ->add('orderStatusTemplates', CollectionType::class, [
                'entry_type' => MailTemplateFormType::class,
            ])
            ->add('domainId', HiddenType::class)
            ->add('save', SubmitType::class);
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
