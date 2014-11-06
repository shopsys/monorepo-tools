<?php

namespace SS6\ShopBundle\Component\Validator;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Auto {

	const OPTION_ENTITY = 'entity';

	private $entity;

	public function __construct(array $options) {
		if (!isset($options[self::OPTION_ENTITY])) {
			throw new \SS6\ShopBundle\Component\Validator\Exception\EntityOptionNotSpecifiedException();
		}

		$this->entity = $options[self::OPTION_ENTITY];
	}

	public function getEntity() {
		return $this->entity;
	}

}
