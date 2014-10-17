<?php

namespace SS6\ShopBundle\Form\Admin\Slider;

use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class SliderItemFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload 
	 */
	private $fileUpload;

	/**
	 * @var bool
	 */
	private $scenarioCreate;
	
	/**
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param bool $scenarioCreate
	 */
	public function __construct(FileUpload $fileUpload, $scenarioCreate = false) {
		$this->fileUpload = $fileUpload;
		$this->scenarioCreate = $scenarioCreate;
	}

	public function getName() {
		return 'sliderItem';
	}

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Slider\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte název')),
				)
			))
			->add('image', new FileUploadType($this->fileUpload), array(
				'required' => $this->scenarioCreate,
				'file_constraints' => array(
					new Constraints\Image(array(
						'mimeTypes' => array('image/png', 'image/jpg', 'image/jpeg'),
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg nebo png',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					)),
				),
			))
			->add('link', 'url', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte odkaz')),
					new Constraints\Url(array('message' => 'Odkaz musí být validní URL adresa'))
				)
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => SliderItemData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
