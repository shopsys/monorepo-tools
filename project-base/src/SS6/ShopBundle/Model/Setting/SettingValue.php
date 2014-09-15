<?php

namespace SS6\ShopBundle\Model\Setting;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="settings3")
 * @ORM\Entity
 */
class SettingValue {

	const TYPE_STRING = 'string';
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_NULL = 'none';

	const BOOLEAN_TRUE = 'true';
	const BOOLEAN_FALSE = 'false';
	
	/**
	 * @var string
	 * 
	 * @ORM\Column(type="string", length=255)
	 * @ORM\Id
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 * @ORM\Id
	 */
	private $value;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=7)
	 * @ORM\Id
	 */
	private $type;

	/**
	 * @param string $name
	 * @param string|int|float|bool|null $value
	 */
	public function __construct($name, $value) {
		$this->name = $name;
		$this->setValue($value);
	}

	/**
	 * @param string|int|float|bool|null $value
	 */
	public function edit($value) {
		$this->setValue($value);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|int|float|bool|null
	 */
	public function getValue() {
		switch ($this->type) {
			case self::TYPE_INTEGER:
				return (int)$this->value;
			case self::TYPE_FLOAT:
				return (float)$this->value;
			case self::TYPE_BOOLEAN:
				return $this->value === self::BOOLEAN_TRUE;
			default:
				return $this->value;
		}
	}

	/**
	 * @param string|int|float|bool|null $value
	 */
	private function setValue($value) {
		$this->type = $this->getValueType($value);
		if ($this->type === self::TYPE_BOOLEAN) {
			$this->value = $value === true ? self::BOOLEAN_TRUE : self::BOOLEAN_FALSE;
		} elseif ($this->type === self::TYPE_NULL) {
			$this->value = $value;
		} else {
			$this->value = (string)$value;
		}
	}

	/**
	 * @param string|int|float|bool|null $value
	 * @return string
	 * @throws \SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException
	 */
	private function getValueType($value) {
		if (is_int($value)) {
			return self::TYPE_INTEGER;
		} elseif (is_float($value)) {
			return self::TYPE_FLOAT;
		} elseif (is_bool($value)) {
			return self::TYPE_BOOLEAN;
		} elseif (is_string($value)) {
			return self::TYPE_STRING;
		} elseif (is_null($value)) {
			return self::TYPE_NULL;
		}

		$message = 'Setting value type of "' . gettype($value) . '" is unsupported.'
			. ' Supported is string, integer, float, boolean or null.';
		throw new \SS6\ShopBundle\Model\Setting\Exception\InvalidArgumentException($message);
	}

}
