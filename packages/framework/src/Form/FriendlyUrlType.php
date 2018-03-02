<?php

namespace Shopsys\FrameworkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class FriendlyUrlType extends AbstractType
{
    const FIELD_DOMAIN = 'domain';
    const FIELD_SLUG = 'slug';

    const SLUG_REGEX = '/^[\w_\-\/]+$/';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(self::FIELD_DOMAIN, DomainType::class, [
            'displayUrl' => true,
            'required' => true,
        ]);
        $builder->add(self::FIELD_SLUG, TextType::class, [
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Regex(self::SLUG_REGEX),
            ],
        ]);
    }
}
