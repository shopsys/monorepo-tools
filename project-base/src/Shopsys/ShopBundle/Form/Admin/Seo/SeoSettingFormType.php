<?php

namespace Shopsys\ShopBundle\Form\Admin\Seo;

use Shopsys\ShopBundle\Component\Constraints\NotInArray;
use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoSettingFormType extends AbstractType
{
    /**
     * @var string[]
     */
    private $titlesOnOtherDomains;

    /**
     * @var string[]
     */
    private $titleAddOnsOnOtherDomains;

    /**
     * @var string[]
     */
    private $descriptionsOnOtherDomains;

    /**
     * @param string[] $titlesOnOtherDomains
     * @param string[] $titleAddOnsOnOtherDomains
     * @param string[] $descriptionsOnOtherDomains
     */
    public function __construct(
        array $titlesOnOtherDomains,
        array $titleAddOnsOnOtherDomains,
        array $descriptionsOnOtherDomains
    ) {
        $this->titlesOnOtherDomains = $titlesOnOtherDomains;
        $this->titleAddOnsOnOtherDomains = $titleAddOnsOnOtherDomains;
        $this->descriptionsOnOtherDomains = $descriptionsOnOtherDomains;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => $this->titlesOnOtherDomains,
                        'message' => 'Same title is used on another domain',
                    ]),
                ],
            ])
            ->add('titleAddOn', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => $this->titleAddOnsOnOtherDomains,
                        'message' => 'Same title complement is used on another domain',
                    ]),
                ],
            ])
            ->add('metaDescription', FormType::TEXTAREA, [
                'required' => false,
                'constraints' => [
                    new NotInArray([
                        'array' => $this->descriptionsOnOtherDomains,
                        'message' => 'Same description is used on another domain',
                    ]),
                ],
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
