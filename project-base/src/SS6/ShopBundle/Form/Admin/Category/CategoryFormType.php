<?php

namespace SS6\ShopBundle\Form\Admin\Category;

use SS6\ShopBundle\Component\Constraints\NotSelectedDomainToShow;
use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;
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
	 * @var \SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer
	 */
	private $inverseArrayValuesTransformer;

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @param \SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer
	 */
	public function __construct(
		array $categories,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer
	) {
		$this->categories = $categories;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
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
			->add($builder
				->create('showOnDomains', FormType::DOMAINS, [
					'constraints' => [
						new NotSelectedDomainToShow(['message' => 'Musíte vybrat alespoň jednu doménu']),
					],
					'property_path' => 'hiddenOnDomains',
				])
				->addViewTransformer($this->inverseArrayValuesTransformer))
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
