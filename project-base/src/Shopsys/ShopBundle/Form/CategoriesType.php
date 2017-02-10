<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Form\Extension\IndexedObjectChoiceList;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Category\Detail\CategoryDetailFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoriesType extends AbstractType
{

    const OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID = 'muted_not_visible_on_domain_id';

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetailFactory
     */
    private $categoryDetailFactory;

    public function __construct(
        CategoryFacade $categoryFacade,
        CategoryDetailFactory $categoryDetailFactory
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->categoryDetailFactory = $categoryDetailFactory;
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['categoryDetails'] = $this->categoryDetailFactory->createDetailsHierarchy($options['choice_list']->getChoices());
        if (isset($options[self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID])) {
            $view->vars['mutedNotVisibleOnDomainId'] = $options[self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID];
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $categories = $this->categoryFacade->getAll();

        $resolver->setDefaults([
            'choice_list' => new IndexedObjectChoiceList($categories, 'id', 'name', [], null, 'id'),
            'multiple' => true,
            'expanded' => true,
        ]);

        $resolver->setOptional([
            self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID,
        ]);

        $resolver->setAllowedTypes([
            self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID => 'int',
        ]);
    }

    /**
     * @return string
     */
    public function getParent() {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName() {
        return 'categories';
    }

}
