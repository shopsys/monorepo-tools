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
			->add('name', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím název')),
				),
			))
			->add('text', 'ckeditor', array('required' => false))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => ArticleData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
