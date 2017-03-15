<?php

namespace Shopsys\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SingleCheckboxChoiceType extends AbstractType
{
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => true,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        foreach ($builder->all() as $i => $child) {
            /* @var $child \Symfony\Component\Form\FormBuilderInterface */
            $options = $child->getOptions();
            $builder->remove($i);
            $options['required'] = false;
            $builder->add($i, CheckboxType::class, $options);
        }
    }
}
