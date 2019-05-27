<?php

namespace Shopsys\FrameworkBundle\Form\Admin\PersonalData;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonalDataFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('personalDataDisplaySiteContent', CKEditorType::class, [
                'required' => false,
            ])
            ->add('personalDataExportSiteContent', CKEditorType::class, [
                'required' => false,

                ])
            ->add('save', SubmitType::class);
    }
}
