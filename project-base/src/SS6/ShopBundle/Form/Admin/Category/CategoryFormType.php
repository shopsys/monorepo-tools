<?php

namespace SS6\ShopBundle\Form\Admin\Category;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Category\CategoryData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CategoryFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category[]
	 */
	private $categories;

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 */
	public function __construct(array $categories) {
		$this->categories = $categories;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'category';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::LOCALIZED, [
				'main_constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte název']),
				],
				'options' => ['required' => false],
			])
			->add('parent', FormType::CHOICE, [
				'required' => false,
				'choice_list' => new ObjectChoiceList($this->categories, 'name', [], null, 'id'),
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => CategoryData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
