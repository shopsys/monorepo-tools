<?php

namespace SS6\ShopBundle\Form\Admin\TermsAndConditions;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermsAndConditionsSettingFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Article\Article[]
	 */
	private $articles;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article[] $articles
	 */
	public function __construct(array $articles, Translator $translator) {
		$this->articles = $articles;
		$this->translator = $translator;
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
					'placeholder' => $this->translator->trans('-- Vyberte článek --'),
				])
				->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
