<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Currency;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CurrencyDomainSettingsFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	private $currencies;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency[] $currencies
	 */
	public function __construct(array $currencies) {
		$this->currencies = $currencies;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'currency_domains_settings';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('domainDefaultCurrencies', 'collection', [
				'required' => true,
				'type' => 'choice',
				'options' => [
					'required' => true,
					'choice_list' => new ObjectChoiceList($this->currencies, 'name', [], null, 'id'),
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Prosím zadejte výchozí měnu']),
					],
				],
			])
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
