<?php

namespace Shopsys\ShopBundle\Form\Admin\Article;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleData;
use Shopsys\ShopBundle\Model\Article\ArticlePlacementList;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType
{
    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticlePlacementList
     */
    private $articlePlacementList;

    public function __construct(
        SeoSettingFacade $seoSettingFacade,
        ArticlePlacementList $articlePlacementList
    ) {
        $this->seoSettingFacade = $seoSettingFacade;
        $this->articlePlacementList = $articlePlacementList;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', FormType::TEXT, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article name']),
                ],
            ])
            ->add('hidden', FormType::YES_NO, ['required' => false])
            ->add('text', FormType::WYSIWYG, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter article content']),
                ],
            ])
            ->add('seoTitle', FormType::TEXT, [
                'required' => false,
            ])
            ->add('seoMetaDescription', FormType::TEXTAREA, [
                'required' => false,
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($options['domain_id']),
                ],
            ])
            ->add('urls', FormType::URL_LIST, [
                'route_name' => 'front_article_detail',
                'entity_id' => $options['article'] !== null ? $options['article']->getId() : null,
            ])
            ->add('save', FormType::SUBMIT);

        if ($options['article'] === null) {
            $builder
                ->add('domainId', FormType::DOMAIN, ['required' => true])
                ->add('placement', FormType::CHOICE, [
                    'required' => true,
                    'choices' => $this->articlePlacementList->getTranslationsIndexedByValue(),
                    'placeholder' => t('-- Choose article position --'),
                    'constraints' => [
                        new Constraints\NotBlank(['message' => 'Please choose article placement']),
                    ],
                ]);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['article', 'domain_id'])
            ->setAllowedTypes('article', [Article::class, 'null'])
            ->setAllowedTypes('domain_id', 'int')
            ->setDefaults([
                'data_class' => ArticleData::class,
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }
}
