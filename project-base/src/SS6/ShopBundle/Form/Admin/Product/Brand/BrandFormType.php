<?php

namespace SS6\ShopBundle\Form\Admin\Product\Brand;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\Brand\Brand;
use SS6\ShopBundle\Model\Product\Brand\BrandData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BrandFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand|null
	 */
	private $brand;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand|null $brand
	 */
	public function __construct(Brand $brand = null) {
		$this->brand = $brand;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'brand_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím zadejte název']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('descriptions', FormType::LOCALIZED, [
				'type' => FormType::WYSIWYG,
				'required' => false,
			])
			->add('urls', FormType::URL_LIST, [
				'route_name' => 'front_brand_detail',
				'entity_id' => $this->brand === null ? null : $this->brand->getId(),
			])
			->add('image', FormType::FILE_UPLOAD, [
				'required' => false,
				'file_constraints' => [
					new Constraints\Image([
						'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png nebo gif',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					]),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => BrandData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
