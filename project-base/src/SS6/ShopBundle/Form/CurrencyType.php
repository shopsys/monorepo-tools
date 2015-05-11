<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Model\Localization\IntlCurrencyRepository;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CurrencyType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Localization\IntlCurrencyRepository
	 */
	private $intlCurrencyRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		IntlCurrencyRepository $intlCurrencyRepository,
		Localization $localization
	) {
		$this->intlCurrencyRepository = $intlCurrencyRepository;
		$this->localization = $localization;
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$intlCurrencies = $this->intlCurrencyRepository->getAll($this->localization->getLocale());

		$choices = [];
		foreach ($intlCurrencies as $intlCurrency) {
			$choices[] = $intlCurrency->getCurrencyCode();
		}

		$resolver->setDefaults([
			'constraints' => [
				new Constraints\Choice([
					'choices' => $choices,
					'message' => 'Prosím zadejte platný třímístný kód měny podle standardu ISO 4217 (velkými písmeny)',
				]),
			],
		]);
	}

	/**
	 * @return string
	 */
	public function getParent() {
		return 'text';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'currency';
	}

}
