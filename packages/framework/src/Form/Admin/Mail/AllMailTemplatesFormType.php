<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Mail;

use Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\FrameworkBundle\Model\Mail\AllMailTemplatesData;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataExportMail;
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

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail
     */
    private $personalDataAccessMail;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataExportMail
     */
    private $personalDataExportMail;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\Mail\ResetPasswordMail $resetPasswordMail
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMail $personalDataAccessMail
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataExportMail $personalDataExportMail
     */
    public function __construct(
        ResetPasswordMail $resetPasswordMail,
        PersonalDataAccessMail $personalDataAccessMail,
        PersonalDataExportMail $personalDataExportMail
    ) {
        $this->resetPasswordMail = $resetPasswordMail;
        $this->personalDataAccessMail = $personalDataAccessMail;
        $this->personalDataExportMail = $personalDataExportMail;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('registrationTemplate', MailTemplateFormType::class)
            ->add('personalDataAccessTemplate', MailTemplateFormType::class, [
                'required_body_variables' => $this->personalDataAccessMail->getRequiredBodyVariables(),
            ])
            ->add('personalDataExportTemplate', MailTemplateFormType::class, [
                'required_body_variables' => $this->personalDataExportMail->getRequiredBodyVariables(),
            ])
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
