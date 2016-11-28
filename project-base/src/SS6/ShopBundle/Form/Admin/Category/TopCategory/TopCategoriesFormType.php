<?php

namespace SS6\ShopBundle\Form\Admin\Category\TopCategory;

use SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer;
use SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopCategoriesFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
	 */
	private $removeDuplicatesTransformer;

	/**
	 * @var string[]
	 */
	private $categoryNamesIndexedByIds;

	/**
	 * @var \SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer
	 */
	private $categoriesIdsToCategoriesTransformer;

	/**
	 * @param string[] $categoryNamesIndexedByIds
	 * @param \SS6\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
	 * @param \SS6\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
	 */
	public function __construct(
		array $categoryNamesIndexedByIds,
		RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
		CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
	) {
		$this->categoryNamesIndexedByIds = $categoryNamesIndexedByIds;
		$this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
		$this->categoriesIdsToCategoriesTransformer = $categoriesIdsToCategoriesTransformer;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'top_categories_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add(
				$builder
					->create('categories', FormType::SORTABLE_VALUES, [
						'labels_by_value' => $this->categoryNamesIndexedByIds,
						'required' => false,
					])
					->addViewTransformer($this->removeDuplicatesTransformer)
					->addModelTransformer($this->categoriesIdsToCategoriesTransformer)
			)
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
