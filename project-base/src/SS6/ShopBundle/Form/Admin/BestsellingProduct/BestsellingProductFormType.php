<?php

namespace SS6\ShopBundle\Form\Admin\BestsellingProduct;

use SS6\ShopBundle\Component\Constraints;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BestsellingProductFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'bestselling_product';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('bestsellingProducts', FormType::FORM, [
				'error_bubbling' => true,
				'constraints' => [
					new Constraints\UniqueCollection([
						'allowEmpty' => true,
						'message' => 'Zadali jste duplicitní zboží. '
							. 'V seznamu nejprodávanějšího zboží musí být každé zboží jen jedenkrát. '
							. 'Prosím chybu opravte a poté znovu uložte.',
					]),
				],
			])
			->add('save', FormType::SUBMIT);

		for ($i = 0; $i < 10; $i++) {
			$builder->get('bestsellingProducts')
				->add($i, FormType::PRODUCT, [
					'required' => false,
					'placeholder' => null,
				]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
