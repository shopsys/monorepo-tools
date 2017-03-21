<?php

namespace Shopsys\ShopBundle\Form\Front\Product;

use Shopsys\ShopBundle\Model\Product\Filter\PriceRange;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductFilterFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $priceScale = 2;
        $priceTransformer = new MoneyToLocalizedStringTransformer($priceScale, false);
        $config = $options['product_filter_config'];
        /* @var $config \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterConfig */

        $builder
            ->add('minimalPrice', MoneyType::class, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $priceTransformer->transform($config->getPriceRange()->getMaximalPrice())],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Price must be greater or equal to {{ compared_value }}',
                    ]),
                ],
            ])
            ->add('maximalPrice', MoneyType::class, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $priceTransformer->transform($config->getPriceRange()->getMaximalPrice())],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Price must be greater or equal to {{ compared_value }}',
                    ]),
                ],
            ])
            ->add('parameters', ParameterFilterFormType::class, [
                'required' => false,
                'product_filter_config' => $config,
            ])
            ->add('inStock', CheckboxType::class, ['required' => false])
            ->add('flags', ChoiceType::class, [
                'required' => false,
                'choices' => $config->getFlagChoices(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_name' => 'id',
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('brands', ChoiceType::class, [
                'required' => false,
                'choices' => $config->getBrandChoices(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_name' => 'id',
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('search', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('product_filter_config')
            ->setAllowedTypes('product_filter_config', ProductFilterConfig::class)
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
                'data_class' => ProductFilterData::class,
                'method' => 'GET',
                'csrf_protection' => false,
            ]);
    }
}
