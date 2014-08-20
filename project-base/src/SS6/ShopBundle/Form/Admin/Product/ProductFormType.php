<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\DatePickerType;
use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Form\YesNoType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Product\ProductData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat[]
	 */
	private $vats;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload, array $vats) {
		$this->fileUpload = $fileUpload;
		$this->vats = $vats;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte název')),
				),
			))
			->add('catnum', 'text', array(
				'required' => false,
				'constraints' => array(
					new Constraints\Length(array('max' => 100, 'maxMessage' => 'Katalogové číslo nesmí být delší než {{ limit }} znaků')),
				),
			))
			->add('partno', 'text', array(
				'required' => false,
				'constraints' => array(
					new Constraints\Length(array('max' => 100, 'maxMessage' => 'Výrobní číslo nesmí být delší než {{ limit }} znaků')),
				),
			))
			->add('ean', 'text', array(
				'required' => false,
				'constraints' => array(
					new Constraints\Length(array('max' => 100, 'maxMessage' => 'EAN nesmí být delší než {{ limit }} znaků')),
				),
			))
			->add('description', 'ckeditor', array('required' => false))
			->add('price', 'money', array(
				'currency' => false,
				'precision' => 6,
				'required' => false,
				'invalid_message' => 'Prosím zadejte cenu v platném formátu',
			))
			->add('vat', 'choice', array(
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', array(), null, 'id'),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte výši DPH')),
				),
			))
			->add('sellingFrom', new DatePickerType(), array(
				'required' => false,
				'constraints' => array(
					new Constraints\Date(array('message' => 'Datum zadávejte ve formátu dd.mm.rrrr')),
				),
				'invalid_message' => 'Datum zadávejte ve formátu dd.mm.rrrr',
			))
			->add('sellingTo', new DatePickerType(), array(
				'required' => false,
				'constraints' => array(
					new Constraints\Date(array('message' => 'Datum zadávejte ve formátu dd.mm.rrrr')),
				),
				'invalid_message' => 'Datum zadávejte ve formátu dd.mm.rrrr',
			))
			->add('stockQuantity', 'integer', array(
				'required' => false,
				'invalid_message' => 'Prosím zadejte číslo',
			))
			->add('hidden', new YesNoType(), array('required' => false))
			->add('image', new FileUploadType($this->fileUpload), array(
				'required' => false,
				'file_constraints' => array(
					new Constraints\Image(array(
						'mimeTypes' => array('image/png', 'image/jpg', 'image/jpeg'),
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png, gif nebo bmp',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					)),
				),
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => ProductData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
