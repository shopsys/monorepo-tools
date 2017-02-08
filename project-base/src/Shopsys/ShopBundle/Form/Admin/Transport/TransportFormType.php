<?php

namespace Shopsys\ShopBundle\Form\Admin\Transport;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Transport\TransportData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportFormType extends AbstractType {

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat[] $vats
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
					new Constraints\NotBlank(['message' => 'Please enter name']),
				],
				'options' => [
					'required' => false,
					'constraints' => [
						new Constraints\Length(['max' => 255, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
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
					new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
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
						'mimeTypesMessage' => 'Image can be only in JPG, GIF or PNG format',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Uploaded image is to large ({{ size }} {{ suffix }}). '
							. 'Maximum size of an image is {{ limit }} {{ suffix }}.',
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
