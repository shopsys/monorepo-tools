<?php

namespace SS6\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SortableValuesType extends AbstractType {

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'type' => FormType::HIDDEN,
			'allow_add' => true,
			'allow_delete' => true,
			'delete_empty' => true,
			'labels_by_value' => null,
			'placeholder' => false,
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'collection';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'sortable_values';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		$view->vars['labels_by_value'] = $options['labels_by_value'];
		$view->vars['placeholder'] = $options['placeholder'];
	}

}
