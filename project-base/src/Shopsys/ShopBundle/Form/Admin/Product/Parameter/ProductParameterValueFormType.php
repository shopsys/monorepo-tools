<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Parameter;

use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductParameterValueFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade
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
                'choice_list' => new ObjectChoiceList($this->parameterFacade->getAll(), 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose parameter']),
                ],
            ])
            ->add('valueText', LocalizedType::class, [
                'required' => true,
                'main_constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter parameter value']),
                ],
                'options' => [
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
