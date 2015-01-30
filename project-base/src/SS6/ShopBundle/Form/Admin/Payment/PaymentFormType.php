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
			->add('name', 'localized', [
				'main_constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte název']),
				],
			])
			->add('domains', 'domains', [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Musíte vybrat alespoň jednu doménu']),
				],
			])
			->add('hidden', new YesNoType(), ['required' => false])
			->add('transports', 'choice', [
				'choice_list' => new ObjectChoiceList($this->allTransports, 'name', [], null, 'id'),
				'multiple' => true,
				'expanded' => true,
				'required' => false,
			])
			->add('vat', 'choice', [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte výši DPH']),
				],
			])
			->add('description', 'localized', [
				'required' => false,
				'type' => 'textarea',
			])
			->add('instructions', 'localized', [
				'required' => false,
				'type' => 'ckeditor',
			])
			->add('image', new FileUploadType($this->fileUpload), [
				'required' => false,
				'file_constraints' => [
					new Constraints\Image([
						'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png nebo gif',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek ({{ size }} {{ suffix }}) může mít velikost maximálně {{ limit }} {{ suffix }}',
					]),
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => PaymentData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
