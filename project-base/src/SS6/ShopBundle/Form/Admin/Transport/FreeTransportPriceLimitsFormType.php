<?php

namespace SS6\ShopBundle\Form\Admin\Transport;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class FreeTransportPriceLimitsFormType extends AbstractType {

	const DOMAINS_SUBFORM_NAME = 'priceLimits';
	const FIELD_ENABLED = 'enabled';
	const FIELD_PRICE_LIMIT = 'priceLimit';

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private $domains;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency[] $domains
	 */
	public function __construct(array $domains) {
		$this->domains = $domains;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'free_transport_price_limits';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add($this->getPriceLimitsBuilder($builder))
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @return \Symfony\Component\Form\FormBuilderInterface
	 */
	private function getPriceLimitsBuilder(FormBuilderInterface $builder) {
		$formBuilderForDomains = $builder->create(self::DOMAINS_SUBFORM_NAME, null, ['compound' => true]);

		foreach ($this->domains as $domainConfig) {
			$formBuilderForDomain = $builder->create($domainConfig->getId(), null, ['compound' => true])
				->add(self::FIELD_ENABLED, FormType::CHECKBOX, [
					'required' => false,
				])
				->add(self::FIELD_PRICE_LIMIT, FormType::MONEY, [
					'required' => true,
					'currency' => false,
					'constraints' => [
						new Constraints\GreaterThanOrEqual([
							'value' => 0,
							'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
						]),
					]
				]);

			$formBuilderForDomains->add($formBuilderForDomain);
		}

		return $formBuilderForDomains;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
