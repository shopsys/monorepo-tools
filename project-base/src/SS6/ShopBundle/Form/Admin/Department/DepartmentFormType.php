<?php

namespace SS6\ShopBundle\Form\Admin\Department;

use SS6\ShopBundle\Model\Department\DepartmentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DepartmentFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Department\Department[]
	 */
	private $departments;

	/**
	 * @param \SS6\ShopBundle\Model\Department\Department[] $departments
	 */
	public function __construct(array $departments) {
		$this->departments = $departments;
	}

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
			->add('names', 'localized', array(
				'main_constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte název')),
				),
				'options' => array('required' => false),
			))
			->add('parent', 'choice', array(
				'required' => false,
				'choice_list' => new ObjectChoiceList($this->departments, 'name', array(), null, 'id'),
			));
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
