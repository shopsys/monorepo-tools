<?php

namespace Shopsys\ShopBundle\Form\Front\Product;

use Shopsys\FrameworkBundle\Form\Constraints\NotNegativeMoneyAmount;
use Shopsys\FrameworkBundle\Form\Transformers\NumericToMoneyTransformer;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFilterFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $priceScale = 2;
        $modelTransformer = new NumericToMoneyTransformer($priceScale);
        $viewTransformer = new MoneyToLocalizedStringTransformer($priceScale, false);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $config */
        $config = $options['product_filter_config'];

        $builder
            ->add('minimalPrice', MoneyType::class, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $viewTransformer->transform($modelTransformer->transform($config->getPriceRange()->getMinimalPrice()))],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new NotNegativeMoneyAmount(['message' => 'Price must be greater or equal to zero']),
                ],
            ])
            ->add('maximalPrice', MoneyType::class, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $viewTransformer->transform($modelTransformer->transform($config->getPriceRange()->getMaximalPrice()))],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new NotNegativeMoneyAmount(['message' => 'Price must be greater or equal to zero']),
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
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('brands', ChoiceType::class, [
                'required' => false,
                'choices' => $config->getBrandChoices(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_name' => 'id',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('search', SubmitType::class);

        $builder->get('minimalPrice')->addModelTransformer($modelTransformer);
        $builder->get('maximalPrice')->addModelTransformer($modelTransformer);
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
