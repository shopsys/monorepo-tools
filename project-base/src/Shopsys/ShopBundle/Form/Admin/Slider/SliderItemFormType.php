<?php

namespace Shopsys\ShopBundle\Form\Admin\Slider;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class SliderItemFormType extends AbstractType
{
    /**
     * @var bool
     */
    private $scenarioCreate;

    /**
     * @param bool $scenarioCreate
     */
    public function __construct($scenarioCreate = false)
    {
        $this->scenarioCreate = $scenarioCreate;
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
                    new Constraints\NotBlank(['message' => 'Please enter name']),
                ],
            ])
            ->add('image', FormType::FILE_UPLOAD, [
                'required' => $this->scenarioCreate,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose image',
                        'groups' => 'create',
                    ]),
                ],
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
            ->add('link', FormType::URL, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter link']),
                    new Constraints\Url(['message' => 'Link must be valid URL address']),
                ],
            ])
            ->add('hidden', FormType::YES_NO, [
                'required' => false,
                'constraints' => [
                    new Constraints\NotNull([
                        'message' => 'Please choose visibility',
                    ]),
                ],
            ])
            ->add('save', FormType::SUBMIT);

        if ($this->scenarioCreate) {
            $builder->add('domainId', FormType::DOMAIN, ['required' => true]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $validationGroups = $this->scenarioCreate ? ['Default', 'create'] : ['Default'];

        $resolver->setDefaults([
            'data_class' => SliderItemData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'validation_groups' => $validationGroups,
        ]);
    }
}
