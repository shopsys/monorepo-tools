<?php

namespace Shopsys\ShopBundle\Form\Admin\Cookies;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Article\ArticleFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CookiesSettingFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleFacade
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

        $builder
            ->add('cookiesArticle', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($articles, 'name', [], null, 'id'),
                'placeholder' => t('-- Choose article --'),
            ])
            ->add('save', FormType::SUBMIT);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('domain_id')
            ->setAllowedTypes('domain_id', 'int')
            ->setDefault('attr', ['novalidate' => 'novalidate']);
    }
}
