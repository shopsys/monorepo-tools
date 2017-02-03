<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Form\FormType;
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
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $allTransports
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 */
	public function __construct(array $allTransports, array $vats) {
		$this->allTransports = $allTransports;
		$this->vats = $vats;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('name', FormType::LOCALIZED, [
				'main_constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím název']),
				],
				'options' => [
					'constraints' => [
						new Constraints\Length(['max' => 255, 'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků']),
					],
				],
			])
			->add('domains', FormType::DOMAINS, [
				'required' => false,
			])
			->add('hidden', FormType::YES_NO, ['required' => false])
			->add('czkRounding', FormType::YES_NO, ['required' => false])
			->add('transports', FormType::CHOICE, [
				'choice_list' => new ObjectChoiceList($this->allTransports, 'name', [], null, 'id'),
				'multiple' => true,
				'expanded' => true,
				'required' => false,
			])
			->add('vat', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím výši DPH']),
				],
			])
			->add('description', FormType::LOCALIZED, [
				'required' => false,
				'type' => 'textarea',
			])
			->add('instructions', FormType::LOCALIZED, [
				'required' => false,
				'type' => 'ckeditor',
			])
			->add('image', FormType::FILE_UPLOAD, [
				'required' => false,
				'file_constraints' => [
					new Constraints\Image([
						'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg, png nebo gif',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek je příliš velký ({{ size }} {{ suffix }}). '
							. 'Maximální velikost obrázku je {{ limit }} {{ suffix }}.',
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
