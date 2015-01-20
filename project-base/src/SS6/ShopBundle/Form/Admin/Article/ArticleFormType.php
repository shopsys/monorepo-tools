<?php

namespace SS6\ShopBundle\Form\Admin\Article;

use SS6\ShopBundle\Model\Article\ArticleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'article';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('text', 'ckeditor', ['required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím text článku']),
				],
			])
			->add('domainId', 'domain', ['required' => true])
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ArticleData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
