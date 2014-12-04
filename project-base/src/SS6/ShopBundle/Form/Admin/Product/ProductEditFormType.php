<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Constrains\UniqueCollection;
use SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use SS6\ShopBundle\Form\Admin\Product\ProductFormTypeFactory;
use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Product\ProductEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductEditFormType extends AbstractType {

	const INTENTION = 'product_edit_type';

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
	 * @param \SS6\ShopBundle\Model\Image\Image[] $images
	 * @param \SS6\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param \SS6\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
	 */
	public function __construct(
		array $images,
		ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
		FileUpload $fileUpload,
		ProductFormTypeFactory $productFormTypeFactory
	) {
		$this->images = $images;
		$this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
		$this->fileUpload = $fileUpload;
		$this->productFormTypeFactory = $productFormTypeFactory;
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
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('productData', $this->productFormTypeFactory->create())
			->add('imagesToUpload', new FileUploadType($this->fileUpload), array(
				'required' => false,
				'multiple' => true,
				'file_constraints' => array(
					new Constraints\Image(array(
						'mimeTypes' => array('image/png', 'image/jpg', 'image/jpeg'),
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png, gif nebo bmp',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					)),
				),
			))
			->add('imagesToDelete', 'choice', array(
				'required' => false,
				'multiple' => true,
				'expanded' => true,
				'choice_list' => new ObjectChoiceList($this->images, 'filename', array(), null, 'id'),
			))
			->add('parameters', 'collection', array(
				'required' => false,
				'allow_add' => true,
				'allow_delete' => true,
				'type' => $this->productParameterValueFormTypeFactory->create(),
				'constraints' => array(
					new UniqueCollection(array(
						'fields' => array('parameter'),
						'message' => 'Každý parametr může být nastaven pouze jednou',
					)),
				),
				'error_bubbling' => false,
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => ProductEditData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'intention' => self::INTENTION,
		));
	}

}
