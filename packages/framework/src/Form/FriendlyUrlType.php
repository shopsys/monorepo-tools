<?php

namespace Shopsys\FrameworkBundle\Form;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class FriendlyUrlType extends AbstractType
{
    const SLUG_REGEX = '/^[\w_\-\/]+$/';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(UrlListData::FIELD_DOMAIN, DomainType::class, [
            'displayUrl' => true,
            'required' => true,
        ]);
        $builder->add(UrlListData::FIELD_SLUG, TextType::class, [
            'required' => true,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Regex(self::SLUG_REGEX),
            ],
        ]);
    }
}
