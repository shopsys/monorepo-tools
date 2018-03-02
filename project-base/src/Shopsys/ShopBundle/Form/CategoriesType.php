<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Transformers\CategoriesTypeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Transformers\CategoriesTypeTransformer
     */
    private $categoriesTypeTransformer;

    public function __construct(CategoriesTypeTransformer $categoryTransformer)
    {
        $this->categoriesTypeTransformer = $categoryTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['domain_id'] = $options['domain_id'];
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->categoriesTypeTransformer);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $entryOptionsNormalizer = function (Options $options, $value) {
            $value['domain_id'] = $value['domain_id'] ?? $options['domain_id'];

            return $value;
        };

        $resolver
            ->setRequired('domain_id')
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'required' => false,
                'entry_type' => CategoryCheckboxType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);

        $resolver->setNormalizer('entry_options', $entryOptionsNormalizer);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return CollectionType::class;
    }
}
