<?php

namespace SS6\ShopBundle\Form\Admin\Department;

use SS6\ShopBundle\Model\Department\DepartmentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DepartmentFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'department';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text');
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => DepartmentData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
