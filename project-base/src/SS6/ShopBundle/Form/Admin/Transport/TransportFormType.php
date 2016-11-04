<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Transport\TransportData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 */
	public function __construct(array $vats) {
		$this->vats = $vats;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport_form';
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
					new Constraints\NotBlank(['message' => 'Prosím vyplňte název']),
				],
				'options' => [
					'required' => false,
					'constraints' => [
						new Constraints\Length(['max' => 255, 'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků']),
					],
				],
			])
			->add('domains', FormType::DOMAINS, [
				'required' => false,
			])
			->add('hidden', FormType::YES_NO, ['required' => false])
			->add('vat', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte výši DPH']),
				],
			])
			->add('description', FormType::LOCALIZED, [
				'required' => false,
				'type' => FormType::TEXTAREA,
			])
			->add('instructions', FormType::LOCALIZED, [
				'required' => false,
				'type' => FormType::WYSIWYG,
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
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => TransportData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
