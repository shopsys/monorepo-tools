<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product\Parameter;

use Shopsys\FrameworkBundle\Form\Locale\LocalizedType;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductParameterValueFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterFacade
     */
    private $parameterFacade;

    public function __construct(ParameterFacade $parameterFacade)
    {
        $this->parameterFacade = $parameterFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parameter', ChoiceType::class, [
                'required' => true,
                'choices' => $this->parameterFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose parameter']),
                ],
            ])
            ->add('valueTextsByLocale', LocalizedType::class, [
                'required' => true,
                'main_constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter parameter value']),
                ],
                'entry_options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => ProductParameterValuesLocalizedData::class,
        ]);
    }
}
