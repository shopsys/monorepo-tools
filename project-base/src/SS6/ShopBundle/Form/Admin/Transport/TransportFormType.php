<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\Admin\Transport\TransportFormData;
use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Form\YesNoType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 */
	public function __construct(FileUpload $fileUpload) {
		$this->fileUpload = $fileUpload;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'integer', array('read_only' => true, 'required' => false))
			->add('name', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte název')),
				),
			))
			->add('hidden', new YesNoType(), array('required' => false))
			->add('price', 'money', array(
				'currency' => false,
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte cenu')),
				),
				'invalid_message' => 'Prosím zadejte cenu v platném formátu',
			))
			->add('description', 'textarea', array('required' => false))
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

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => TransportFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}
}
