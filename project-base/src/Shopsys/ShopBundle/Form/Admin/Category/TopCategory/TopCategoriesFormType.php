<?php

namespace Shopsys\ShopBundle\Form\Admin\Category\TopCategory;

use Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopCategoriesFormType extends AbstractType
{

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var string[]
     */
    private $categoryPaths;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer
     */
    private $categoriesIdsToCategoriesTransformer;

    /**
     * @param string[] $categoryPaths
     * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     * @param \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
     */
    public function __construct(
        array $categoryPaths,
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
    ) {
        $this->categoryPaths = $categoryPaths;
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
                        'labels_by_value' => $this->categoryPaths,
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
