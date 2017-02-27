<?php

namespace Shopsys\ShopBundle\Form\Admin\Mail;

use Shopsys\ShopBundle\Component\Constraints\Contains;
use Shopsys\ShopBundle\Component\Constraints\Email;
use Shopsys\ShopBundle\Component\Transformers\EmptyWysiwygTransformer;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Mail\MailTemplateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class MailTemplateFormType extends AbstractType
{
    const VALIDATION_GROUP_SEND_MAIL = 'sendMail';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bccEmail', FormType::EMAIL, [
                'required' => false,
                'constraints' => [
                    new Email(),
                    new Constraints\Length(['max' => 255, 'maxMessage' => 'Email cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('subject', FormType::TEXT, [
                'required' => true,
                'constraints' => $this->getSubjectConstraints($options),
            ])
            ->add(
                $builder
                    ->create('body', FormType::WYSIWYG, [
                        'required' => true,
                        'config_name' => 'email',
                        'constraints' => $this->getBodyConstraints($options),
                    ])
                    ->addModelTransformer(new EmptyWysiwygTransformer())
            )
            ->add('attachment', FormType::FILE_UPLOAD, [
                'required' => false,
                'multiple' => false,
                'file_constraints' => [
                    new Constraints\File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Uploaded file is to large ({{ size }} {{ suffix }}). '
                            . 'Maximum size of an file is {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
            ->add('deleteAttachment', FormType::CHECKBOX)
            ->add('sendMail', FormType::CHECKBOX, ['required' => false]);
    }

    /**
     * @param array $options
     * @return \Symfony\Component\Validator\Constraint[]
     */
    private function getSubjectConstraints(array $options)
    {
        $subjectConstraints = [];

        $subjectConstraints[] = new Constraints\NotBlank([
            'message' => 'Please enter subject',
            'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
        ]);
        $subjectConstraints[] = new Constraints\Length([
            'max' => 255,
            'maxMessage' => 'E-mail subject cannot be longer than {{ limit }} characters',
        ]);

        foreach ($options['required_subject_variables'] as $variableName) {
            $subjectConstraints[] = new Contains([
                'needle' => $variableName,
                'message' => 'Variable {{ needle }} is required',
                'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
            ]);
        }

        return $subjectConstraints;
    }

    /**
     * @param array $options
     * @return \Symfony\Component\Validator\Constraint[]
     */
    private function getBodyConstraints(array $options)
    {
        $bodyConstraints = [];

        $bodyConstraints[] = new Constraints\NotBlank([
            'message' => 'Please enter e-mail content',
            'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
        ]);

        foreach ($options['required_body_variables'] as $variableName) {
            $bodyConstraints[] = new Contains([
                'needle' => $variableName,
                'message' => 'Variable {{ needle }} is required',
                'groups' => [self::VALIDATION_GROUP_SEND_MAIL],
            ]);
        }

        return $bodyConstraints;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['required_subject_variables', 'required_body_variables'])
            ->setAllowedTypes('required_subject_variables', 'array')
            ->setAllowedTypes('required_body_variables', 'array')
            ->setDefaults([
                'required_subject_variables' => [],
                'required_body_variables' => [],
                'data_class' => MailTemplateData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
                    $mailTemplateData = $form->getData();
                    /* @var $mailTemplateData \Shopsys\ShopBundle\Model\Mail\MailTemplateData */

                    if ($mailTemplateData->sendMail) {
                        $validationGroups[] = self::VALIDATION_GROUP_SEND_MAIL;
                    }

                    return $validationGroups;
                },
            ]);
    }
}
