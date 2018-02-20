<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Category;

use Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider;
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
     * @var \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider
     */
    private $dataStorageProvider;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\HeurekaBundle\DataStorageProvider $dataStorageProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        DataStorageProvider $dataStorageProvider
    ) {
        $this->translator = $translator;
        $this->dataStorageProvider = $dataStorageProvider;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param  array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allHeurekaCategories = $this->dataStorageProvider->getHeurekaCategoryDataStorage()->getAll();

        $builder->add('heureka_category', ChoiceType::class, [
            'label' => $this->translator->trans('Heureka category'),
            'choices' => array_column($allHeurekaCategories, 'id', 'name'),
            'required' => false,
            'attr' => ['class' => 'js-autocomplete-selectbox'],
        ]);
    }
}
