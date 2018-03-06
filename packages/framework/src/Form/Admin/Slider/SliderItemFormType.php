<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Slider;

use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\FrameworkBundle\Form\DomainType;
use Shopsys\FrameworkBundle\Form\FileUploadType;
use Shopsys\FrameworkBundle\Model\Slider\SliderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class SliderItemFormType extends AbstractType
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageConstraints = [];
        if ($options['scenario'] === self::SCENARIO_CREATE) {
            $imageConstraints[] = new Constraints\NotBlank(['message' => 'Please choose image']);
        }

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
            ])
            ->add('image', FileUploadType::class, [
                'required' => $options['scenario'] === self::SCENARIO_CREATE,
                'constraints' => $imageConstraints,
                'file_constraints' => [
                    new Constraints\Image([
                        'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
                        'mimeTypesMessage' => 'Image can be only in JPG or PNG format',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Uploaded image is to large ({{ size }} {{ suffix }}). '
                            . 'Maximum size of an image is {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
            ->add('link', UrlType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter link']),
                    new Constraints\Url(['message' => 'Link must be valid URL address']),
                ],
            ])
            ->add('hidden', YesNoType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\NotNull([
                        'message' => 'Please choose visibility',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class);

        if ($options['scenario'] === self::SCENARIO_CREATE) {
            $builder->add('domainId', DomainType::class, ['required' => true]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('scenario')
            ->addAllowedValues('scenario', [self::SCENARIO_CREATE, self::SCENARIO_EDIT])
            ->setDefaults([
                'data_class' => SliderItemData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
