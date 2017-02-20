<?php

namespace Shopsys\ShopBundle\Form\Front\Product;

use Shopsys\ShopBundle\Form\Extension\IndexedObjectChoiceList;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Filter\PriceRange;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductFilterFormType extends AbstractType
{
    const NAME = 'product_filter_form';

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    private $parameterFilterChoices;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\Flag[]
     */
    private $flagFilterChoices;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\Brand[]
     */
    private $brandFilterChoices;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\PriceRange
     */
    private $priceRange;

    /**
     * @param array $parameterFilterChoices
     * @param array $flagFilterChoices
     * @param array $brandFilterChoices
     * @param \Shopsys\ShopBundle\Model\Product\Filter\PriceRange $priceRange
     */
    public function __construct(
        array $parameterFilterChoices,
        array $flagFilterChoices,
        array $brandFilterChoices,
        PriceRange $priceRange
    ) {
        $this->parameterFilterChoices = $parameterFilterChoices;
        $this->flagFilterChoices = $flagFilterChoices;
        $this->brandFilterChoices = $brandFilterChoices;
        $this->priceRange = $priceRange;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $priceScale = 2;
        $priceTransformer = new MoneyToLocalizedStringTransformer($priceScale, false);

        $builder
            ->add('minimalPrice', FormType::MONEY, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $priceTransformer->transform($this->priceRange->getMinimalPrice())],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Price must be greater or equal to {{ compared_value }}',
                    ]),
                ],
            ])
            ->add('maximalPrice', FormType::MONEY, [
                'currency' => false,
                'scale' => $priceScale,
                'required' => false,
                'attr' => ['placeholder' => $priceTransformer->transform($this->priceRange->getMaximalPrice())],
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Price must be greater or equal to {{ compared_value }}',
                    ]),
                ],
            ])
            ->add('parameters', new ParameterFilterFormType($this->parameterFilterChoices), [
                'required' => false,
            ])
            ->add('inStock', FormType::CHECKBOX, ['required' => false])
            ->add('flags', FormType::CHOICE, [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_list' => new IndexedObjectChoiceList($this->flagFilterChoices, 'id', 'name', [], null, 'id'),
            ])
            ->add('brands', FormType::CHOICE, [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_list' => new IndexedObjectChoiceList($this->brandFilterChoices, 'id', 'name', [], null, 'id'),
            ])
            ->add('search', FormType::SUBMIT);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
            'data_class' => ProductFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    public function getParameterFilterChoices()
    {
        return $this->parameterFilterChoices;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Brand\Brand[]
     */
    public function getBrandFilterChoices()
    {
        return $this->brandFilterChoices;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Flag\Flag[]
     */
    public function getFlagFilterChoices()
    {
        return $this->flagFilterChoices;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Filter\PriceRange
     */
    public function getPriceRange()
    {
        return $this->priceRange;
    }
}
