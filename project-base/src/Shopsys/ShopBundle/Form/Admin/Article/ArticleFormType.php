<?php

namespace Shopsys\ShopBundle\Form\Admin\Article;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType
{
    /**
     * @var string[]
     */
    private $articlePlacementLocalizedNamesByName;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\Article|null
     */
    private $article;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\Article|null
     */
    private $defaultSeoMetaDescription;

    /**
     * @param string[]
     * @param \Shopsys\ShopBundle\Model\Article\Article|null $article
     * @param string|null $defaultSeoMetaDescription
     */
    public function __construct(
        $articlePlacementLocalizedNamesByName,
        Article $article = null,
        $defaultSeoMetaDescription = null
    ) {
        $this->articlePlacementLocalizedNamesByName = $articlePlacementLocalizedNamesByName;
        $this->article = $article;
        $this->defaultSeoMetaDescription = $defaultSeoMetaDescription;
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
                    'placeholder' => $this->defaultSeoMetaDescription,
                ],
            ])
            ->add('urls', FormType::URL_LIST, [
                'route_name' => 'front_article_detail',
                'entity_id' => $this->article === null ? null : $this->article->getId(),
            ])
            ->add('save', FormType::SUBMIT);

        if ($this->article === null) {
            $builder
                ->add('domainId', FormType::DOMAIN, ['required' => true])
                ->add('placement', FormType::CHOICE, [
                    'required' => true,
                    'choices' => $this->articlePlacementLocalizedNamesByName,
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
        $resolver->setDefaults([
            'data_class' => ArticleData::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
