<?php

namespace Shopsys\ShopBundle\Form\Admin\TransportAndPayment;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class FreeTransportAndPaymentPriceLimitsFormType extends AbstractType {

	const DOMAINS_SUBFORM_NAME = 'priceLimits';
	const FIELD_ENABLED = 'enabled';
	const FIELD_PRICE_LIMIT = 'priceLimit';
	const VALIDATION_GROUP_PRICE_LIMIT_ENABLED = 'priceLimitEnabled';

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	private $domains;

	/**
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domains
	 */
	public function __construct(array $domains) {
		$this->domains = $domains;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'free_transport_and_payment_price_limits_form';
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
			$formBuilderForDomain = $builder->create(
				$domainConfig->getId(),
				null,
				[
					'compound' => true,
					'validation_groups' => function (FormInterface $form) {
						$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
						$formData = $form->getData();
						if ($formData[FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED]) {
							$validationGroups[] = FreeTransportAndPaymentPriceLimitsFormType::VALIDATION_GROUP_PRICE_LIMIT_ENABLED;
						}

						return $validationGroups;
					},
				]
			);

			$formBuilderForDomain
				->add(self::FIELD_ENABLED, FormType::CHECKBOX, [
					'required' => false,
				])
				->add(self::FIELD_PRICE_LIMIT, FormType::MONEY, [
					'required' => true,
					'currency' => false,
					'constraints' => [
						new Constraints\GreaterThanOrEqual([
							'value' => 0,
							'message' => 'Price must be greater or equal to {{ compared_value }}',
							'groups' => [self::VALIDATION_GROUP_PRICE_LIMIT_ENABLED],
						]),
						new Constraints\NotBlank([
							'message' => 'Please enter price',
							'groups' => [self::VALIDATION_GROUP_PRICE_LIMIT_ENABLED],
						]),
					],
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
