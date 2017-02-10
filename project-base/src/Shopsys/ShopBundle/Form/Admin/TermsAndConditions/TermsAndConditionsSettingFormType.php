<?php

namespace Shopsys\ShopBundle\Form\Admin\TermsAndConditions;

use Shopsys\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermsAndConditionsSettingFormType extends AbstractType
{

    /**
     * @var \Shopsys\ShopBundle\Model\Article\Article[]
     */
    private $articles;

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article[] $articles
     */
    public function __construct(array $articles) {
        $this->articles = $articles;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'terms_and_conditions_setting_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder
                ->add('termsAndConditionsArticle', FormType::CHOICE, [
                    'required' => false,
                    'choice_list' => new ObjectChoiceList($this->articles, 'name', [], null, 'id'),
                    'placeholder' => t('-- Choose article --'),
                ])
                ->add('save', FormType::SUBMIT);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }

}
