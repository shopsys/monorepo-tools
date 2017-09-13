<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Transformers\CategoriesTypeTransformerFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\CategoriesTypeTransformerFactory
     */
    private $categoriesTypeTransformerFactory;

    public function __construct(CategoriesTypeTransformerFactory $categoryTransformerFactory)
    {
        $this->categoriesTypeTransformerFactory = $categoryTransformerFactory;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categoriesTypeTransformer = $this->categoriesTypeTransformerFactory->create($options['domain_id']);

        $builder->addViewTransformer($categoriesTypeTransformer);
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
