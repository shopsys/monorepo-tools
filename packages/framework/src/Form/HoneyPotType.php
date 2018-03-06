<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class HoneyPotType extends AbstractType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'constraints' => new Constraints\Blank(['message' => 'This field must be empty']),
        ]);
    }
}
