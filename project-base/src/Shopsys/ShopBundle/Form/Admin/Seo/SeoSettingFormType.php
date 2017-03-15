<?php

namespace Shopsys\ShopBundle\Form\Admin\Seo;

use Shopsys\ShopBundle\Component\Constraints\NotInArray;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoSettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
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

        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($titlesOnOtherDomains, [null]),
                        'message' => 'Same title is used on another domain',
                    ]),
                ],
            ])
            ->add('titleAddOn', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($titleAddOnsOnOtherDomains, [null]),
                        'message' => 'Same title complement is used on another domain',
                    ]),
                ],
            ])
            ->add('metaDescription', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => array_diff($descriptionsOnOtherDomains, [null]),
                        'message' => 'Same description is used on another domain',
                    ]),
                ],
            ])
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
