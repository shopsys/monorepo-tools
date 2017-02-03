<?php

namespace SS6\ShopBundle\Form\Admin\Slider;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Slider\SliderItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class SliderItemFormType extends AbstractType {

	/**
	 * @var bool
	 */
	private $scenarioCreate;

	/**
	 * @param bool $scenarioCreate
	 */
	public function __construct($scenarioCreate = false) {
		$this->scenarioCreate = $scenarioCreate;
	}

	public function getName() {
		return 'slider_item_form';
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
					new Constraints\NotBlank(['message' => 'Vyplňte prosím název']),
				],
			])
			->add('image', FormType::FILE_UPLOAD, [
				'required' => $this->scenarioCreate,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Prosím vyberte obrázek',
						'groups' => 'create',
					]),
				],
				'file_constraints' => [
					new Constraints\Image([
						'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
						'mimeTypesMessage' => 'Obrázek může být pouze ve formátech jpg nebo png',
						'maxSize' => '2M',
						'maxSizeMessage' => 'Nahraný obrázek je příliš velký ({{ size }} {{ suffix }}). '
							. 'Maximální velikost obrázku je {{ limit }} {{ suffix }}.',
					]),
				],
			])
			->add('link', FormType::URL, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte odkaz']),
					new Constraints\Url(['message' => 'Odkaz musí být validní URL adresa']),
				],
			])
			->add('hidden', FormType::YES_NO, [
				'required' => false,
				'constraints' => [
					new Constraints\NotNull([
						'message' => 'Prosím vyberte viditelnost.',
					]),
				],
			])
			->add('save', FormType::SUBMIT);

		if ($this->scenarioCreate) {
			$builder->add('domainId', FormType::DOMAIN, ['required' => true]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$validationGroups = $this->scenarioCreate ? ['Default', 'create'] : ['Default'];

		$resolver->setDefaults([
			'data_class' => SliderItemData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => $validationGroups,
		]);
	}

}
