<?php

namespace Shopsys\ShopBundle\Form\Admin\Mail;

use Shopsys\ShopBundle\Form\Admin\Mail\MailTemplateFormType;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail;
use Shopsys\ShopBundle\Model\Mail\AllMailTemplatesData;
use Shopsys\ShopBundle\Model\Mail\DummyMailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllMailTemplatesFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\Mail\ResetPasswordMail
     */
    private $resetPasswordMail;

    public function __construct(
        ResetPasswordMail $resetPasswordMail
    ) {
        $this->resetPasswordMail = $resetPasswordMail;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'all_mail_templates_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('registrationTemplate', new MailTemplateFormType(new DummyMailType()))
            ->add('resetPasswordTemplate', new MailTemplateFormType($this->resetPasswordMail))
            ->add('orderStatusTemplates', FormType::COLLECTION, [
                'type' => new MailTemplateFormType(new DummyMailType()),
            ])
            ->add('domainId', FormType::HIDDEN)
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => AllMailTemplatesData::class,
        ]);
    }
}
