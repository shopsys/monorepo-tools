<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Form;

use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryFormType extends AbstractType
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        TranslatorInterface $translator,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->translator = $translator;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param  array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $heurekaCategories = $this->heurekaCategoryFacade->getAllIndexedById();

        $builder->add('heureka_category', ChoiceType::class, [
            'label' => $this->translator->trans('Heureka category'),
            'choices' => $heurekaCategories,
            'required' => false,
            'attr' => ['class' => 'js-autocomplete-selectbox'],
            'choice_label' => 'getName',
        ]);
    }
}
