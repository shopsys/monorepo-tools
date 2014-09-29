<?php

namespace SS6\ShopBundle\Model\Product\Parameter;

use Symfony\Component\Form\DataTransformerInterface;

class ParameterValueToParameterTextTransformer implements DataTransformerInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
	 */
	public function __construct(ParameterRepository $parameterRepository) {
		$this->parameterRepository = $parameterRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterValue|null $value
	 * @return string
	 */
	public function transform($value) {
		if (!$value instanceof ParameterValue) {
			return null;
		}

		return $value->getText();
	}

	/**
	 * @param string $value
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ParameterValue|null
	 */
	public function reverseTransform($value) {
		if (strlen($value) === 0) {
			return null;
		}

		return $this->parameterRepository->findOrCreateParameterValueByValueText($value);
	}

}
