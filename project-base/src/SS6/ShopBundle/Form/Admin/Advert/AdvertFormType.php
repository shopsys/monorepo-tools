<?php

namespace SS6\ShopBundle\Form\Admin\Advert;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Advert\Advert;
use SS6\ShopBundle\Model\Advert\AdvertData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AdvertFormType extends AbstractType {

	const VALIDATION_GROUP_TYPE_IMAGE = 'typeImage';
	const VALIDATION_GROUP_TYPE_CODE = 'typeCode';

	/**
	 * @var array
	 */
	private $advertPositionsLocalizedNamesByName;

	/**
	 * @var bool
	 */
	private $imageUploaded;

	/**
	 * @param bool $imageUploaded
	 * @param array $advertPositionsLocalizedNamesByName
	 */
	public function __construct($imageUploaded, array $advertPositionsLocalizedNamesByName) {
		$this->imageUploaded = $imageUploaded;
		$this->advertPositionsLocalizedNamesByName = $advertPositionsLocalizedNamesByName;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'advert_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$imageConstraints = [
			new Constraints\NotBlank([
				'message' => 'Vyberte obrázek',
				'groups' => [self::VALIDATION_GROUP_TYPE_IMAGE],
			]),
		];
		$builder
			->add('domainId', FormType::DOMAIN, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(),
				],
			])
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Zadejte prosím název reklamní plochy']),
				],
			])
			->add('type', FormType::CHOICE, [
				'required' => true,
				'choices' => $this->getTypeChoices(),
				'expanded' => true,
				'multiple' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte typ reklamy']),
				],
			])
			->add('positionName', FormType::CHOICE, [
				'required' => true,
				'choices' => $this->advertPositionsLocalizedNamesByName,
				'placeholder' => t('-- Vyberte plochu --'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyberte reklamní plochu']),
				],
			])
			->add('code', FormType::TEXTAREA, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím HTML kód pro reklamní plochu',
						'groups' => [self::VALIDATION_GROUP_TYPE_CODE],
					]),
				],
			])
			->add('hidden', FormType::YES_NO, ['required' => false])
			->add('link', FormType::TEXT, ['required' => false])
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
				'constraints' => ($this->imageUploaded ? [] : $imageConstraints),
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => AdvertData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];

				$advertData = $form->getData();
				/* @var $advertData \SS6\ShopBundle\Model\Advert\AdvertData */

				if ($advertData->type === Advert::TYPE_CODE) {
					$validationGroups[] = self::VALIDATION_GROUP_TYPE_CODE;
				} elseif ($advertData->type === Advert::TYPE_IMAGE) {
					$validationGroups[] = self::VALIDATION_GROUP_TYPE_IMAGE;
				}
				return $validationGroups;
			},
		]);
	}

	/**
	 * @return string[]
	 */
	private function getTypeChoices() {
		return [
			Advert::TYPE_CODE => t('HTML kód'),
			Advert::TYPE_IMAGE => t('Obrázek s odkazem'),
		];
	}

}
