<?php

namespace SS6\ShopBundle\Form\Admin\Product\Brand;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\Brand\BrandData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class BrandFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'brand_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím zadejte název']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'Název nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => BrandData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
