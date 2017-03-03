<?php

namespace Shopsys\ShopBundle\Form\Admin\Category\TopCategory;

use Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopCategoriesFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer
     */
    private $categoriesIdsToCategoriesTransformer;

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     * @param \Shopsys\ShopBundle\Component\Transformers\CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
     */
    public function __construct(
        CategoryFacade $categoryFacade,
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        CategoriesIdsToCategoriesTransformer $categoriesIdsToCategoriesTransformer
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->categoriesIdsToCategoriesTransformer = $categoriesIdsToCategoriesTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoryPaths = $this->categoryFacade->getFullPathsIndexedByIdsForDomain(
            $options['domain_id'],
            $options['locale']
        );

        $builder
            ->add(
                $builder
                    ->create('categories', FormType::SORTABLE_VALUES, [
                        'labels_by_value' => $categoryPaths,
                        'required' => false,
                    ])
                    ->addViewTransformer($this->removeDuplicatesTransformer)
                    ->addModelTransformer($this->categoriesIdsToCategoriesTransformer)
            )
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['domain_id', 'locale'])
            ->setAllowedTypes('domain_id', 'int')
            ->setAllowedTypes('locale', 'string')
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
