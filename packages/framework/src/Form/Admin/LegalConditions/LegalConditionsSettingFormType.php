<?php

namespace Shopsys\FrameworkBundle\Form\Admin\LegalConditions;

use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegalConditionsSettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    public function __construct(ArticleFacade $articleFacade)
    {
        $this->articleFacade = $articleFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $articles = $this->articleFacade->getAllByDomainId($options['domain_id']);

        $builderSettingsGroup = $builder->create('settings', GroupType::class, [
            'label' => t('Settings'),
            'is_group_container_to_render_as_the_last_one' => true,
        ]);

        $builderSettingsGroup
            ->add('termsAndConditionsArticle', ChoiceType::class, [
                'required' => false,
                'choices' => $articles,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => t('-- Choose article --'),
                'label' => t('Terms and conditions'),
                'icon_title' => t('Choose article, which will serve as terms and conditions with which the customer has to agree when creating order.'),
            ])
            ->add('privacyPolicyArticle', ChoiceType::class, [
                'required' => false,
                'choices' => $articles,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => t('-- Choose article --'),
                'label' => t('Privacy policy'),
                'icon_title' => t('Choose article, which will serve as privacy policy with which the customer has to agree when creating order or registering user.'),
            ]);

        $builder
            ->add($builderSettingsGroup)
            ->add('save', SubmitType::class);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('domain_id')
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
