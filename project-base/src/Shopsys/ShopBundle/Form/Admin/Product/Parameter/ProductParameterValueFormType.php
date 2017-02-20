<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Parameter;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductParameterValueFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter[]
     */
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parameter', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->parameters, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please choose parameter']),
                ],
            ])
            ->add('valueText', FormType::LOCALIZED, [
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
