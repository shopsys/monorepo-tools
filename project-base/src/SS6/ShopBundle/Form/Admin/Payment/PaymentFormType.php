<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Form\FileUploadType;
use SS6\ShopBundle\Form\YesNoType;
use SS6\ShopBundle\Model\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Payment\PaymentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PaymentFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $allTransports;

	/**
	 * @var \SS6\ShopBundle\Model\FileUpload\FileUpload
	 */
	private $fileUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $allTransports
	 * @param \SS6\ShopBundle\Model\FileUpload\FileUpload $fileUpload
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 */
	public function __construct(array $allTransports, FileUpload $fileUpload, array $vats) {
		$this->allTransports = $allTransports;
		$this->fileUpload = $fileUpload;
		$this->vats = $vats;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment';
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
			->add('hidden', new YesNoType(), array('required' => false))
			->add('transports', 'choice', array(
				'choice_list' => new ObjectChoiceList($this->allTransports, 'name', array(), null, 'id'),
				'multiple' => true,
				'expanded' => true,
				'required' => false,
			))
			->add('price', 'money', array(
				'currency' => false,
				'precision' => 6,
				'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte cenu')),
					new Constraints\GreaterThanOrEqual(array(
						'value' => 0,
						'message' => 'Cena musí být větší nebo rovna {{ compared_value }}'
					)),
				),
				'invalid_message' => 'Prosím zadejte cenu v platném formátu',
			))
			->add('vat', 'choice', array(
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', array(), null, 'id'),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte výši DPH')),
				),
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
			'data_class' => PaymentData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}
}
