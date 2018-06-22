<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Seo;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\Constraints\NotInArray;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoSettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    public function __construct(
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $titlesOnOtherDomains = [];
        $titleAddOnsOnOtherDomains = [];
        $descriptionsOnOtherDomains = [];
        foreach ($this->domain->getAllIds() as $domainId) {
            if ($domainId !== $options['domain_id']) {
                $titlesOnOtherDomains[] = $this->seoSettingFacade->getTitleMainPage($domainId);
                $titleAddOnsOnOtherDomains[] = $this->seoSettingFacade->getTitleAddOn($domainId);
                $descriptionsOnOtherDomains[] = $this->seoSettingFacade->getDescriptionMainPage($domainId);
            }
        }

        $builderSettingsGroup = $builder->create('settings', GroupType::class, [
            'required' => false,
            'is_group_container_to_render_as_the_last_one' => true,
            'label' => t('Settings'),
        ]);

        $builderSettingsGroup
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($titlesOnOtherDomains, [null]),
                        'message' => 'Same title is used on another domain',
                    ]),
                ],
                'label' => t('Headline'),
            ])
            ->add('titleAddOn', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($titleAddOnsOnOtherDomains, [null]),
                        'message' => 'Same title complement is used on another domain',
                    ]),
                ],
                'label' => t('Complement to title'),
                'icon_title' => 'Complement to title will be set as suffix to all titles e.g. if complement is set “ | My shop” and product name is “iPhone S7” the result title for this products page will be “iPhone S7 | My shop”.',
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($descriptionsOnOtherDomains, [null]),
                        'message' => 'Same description is used on another domain',
                    ]),
                ],
                'label' => t('Meta description'),
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
            ->addAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
