<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Category\Detail\CategoryDetailFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['categoryDetails'] = $this->categoryDetailFactory->createDetailsHierarchy($options['choices']);
        if (isset($options[self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID])) {
            $view->vars['mutedNotVisibleOnDomainId'] = $options[self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID];
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $categories = $this->categoryFacade->getAll();

        $resolver
            ->setDefined(self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID)
            ->setAllowedTypes(self::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID, 'int')
            ->setDefaults([
                'choices' => $categories,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_name' => 'id',
                'choices_as_values' => true, // Switches to Symfony 3 choice mode, remove after upgrade from 2.8
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
