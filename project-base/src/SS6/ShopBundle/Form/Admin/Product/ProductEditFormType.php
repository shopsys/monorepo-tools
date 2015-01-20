<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Constraints\UniqueCollection;
use SS6\ShopBundle\Component\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer;
use SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use SS6\ShopBundle\Form\Admin\Product\ProductFormTypeFactory;
use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductEditFormType extends AbstractType {

	const INTENTION = 'product_edit_type';
	const VALIDATION_GROUP_MANUAL_PRICE_CALCULATION = 'manualPriceCalculation';

	/**
	 * @var \SS6\ShopBundle\Model\Image\Image[]
	 */
	private $images;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory
	 */
	private $productParameterValueFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
	 */
	private $productFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;

	/**
	 * @param \SS6\ShopBundle\Model\Image\Image[] $images
	 * @param \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param \SS6\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
	 */
	public function __construct(
		array $images,
		ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
		FileUpload $fileUpload,
		ProductFormTypeFactory $productFormTypeFactory,
		array $pricingGroups
	) {
		$this->images = $images;
		$this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
		$this->fileUpload = $fileUpload;
		$this->productFormTypeFactory = $productFormTypeFactory;
		$this->pricingGroups = $pricingGroups;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_edit';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('productData', $this->productFormTypeFactory->create())
			->add('imagesToUpload', new FileUploadType($this->fileUpload), [
				'required' => false,
				'multiple' => true,
				'file_constraints' => [
					new Constraints\Image([
						'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png nebo gif',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					]),
				],
			])
			->add('imagesToDelete', 'choice', [
				'required' => false,
				'multiple' => true,
				'expanded' => true,
				'choice_list' => new ObjectChoiceList($this->images, 'filename', [], null, 'id'),
			])
			->add($builder->create('parameters', 'collection', [
					'required' => false,
					'allow_add' => true,
					'allow_delete' => true,
					'type' => $this->productParameterValueFormTypeFactory->create(),
					'constraints' => [
						new UniqueCollection([
							'fields' => ['parameter', 'locale'],
							'message' => 'Každý parametr může být nastaven pouze jednou',
						]),
					],
					'error_bubbling' => false,
				])
				->addViewTransformer(new ProductParameterValueToProductParameterValuesLocalizedTransformer())
			)
			->add('manualInputPrices', 'form', [
				'compound' => true,
			])
			->add('save', 'submit');

		foreach ($this->pricingGroups as $pricingGroup) {
			$builder->get('manualInputPrices')
				->add($pricingGroup->getId(), 'money', [
					'currency' => false,
					'precision' => 6,
					'required' => true,
					'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
					'constraints' => [
						new Constraints\NotBlank([
							'message' => 'Prosím vyplňte cenu',
							'groups' => [self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION],
						]),
						new Constraints\GreaterThan([
							'value' => 0,
							'message' => 'Cena musí být větší než 0',
							'groups' => [self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION],
						]),
					],
				]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ProductEditData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'intention' => self::INTENTION,
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = ['Default'];
				$productData = $form->getData()->productData;
				/* @var $productData \SS6\ShopBundle\Model\Product\ProductData */

				if ($productData->priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
					$validationGroups[] = self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION;
				}

				return $validationGroups;
			},
		]);
	}

}
