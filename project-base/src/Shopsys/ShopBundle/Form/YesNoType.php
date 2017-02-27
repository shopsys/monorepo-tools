<?php

namespace Shopsys\ShopBundle\Form;

use Shopsys\ShopBundle\Component\Transformers\NoopDataTransformer;
use Shopsys\ShopBundle\Form\Extension\IndexedChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YesNoType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\NoopDataTransformer
     */
    private $noopDataTransformer;

    public function __construct(
        NoopDataTransformer $noopDataTransformer
    ) {
        $this->noopDataTransformer = $noopDataTransformer;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // workaround for ChoiceType issue: https://github.com/symfony/symfony/issues/15573
        $builder->addViewTransformer(new NoopDataTransformer());
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choice_list' => new IndexedChoiceList(
                [true, false],
                [
                    t('Yes'),
                    t('No'),
                ],
                ['yes', 'no'],
                ['1', '0']
            ),
            'multiple' => false,
            'expanded' => true,
            'placeholder' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yes_no';
    }
}
