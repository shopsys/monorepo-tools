<?php

namespace Shopsys\FrameworkBundle\Component\Setting;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\DateTimeHelper\DateTimeHelper;
use Shopsys\FrameworkBundle\Component\Money\Money;

/**
 * @ORM\Table(name="setting_values")
 * @ORM\Entity
 */
class SettingValue
{
    const DATETIME_STORED_FORMAT = DateTime::ISO8601;

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATETIME = 'datetime';
    const TYPE_MONEY = 'money';
    const TYPE_NULL = 'none';

    const BOOLEAN_TRUE = 'true';
    const BOOLEAN_FALSE = 'false';

    const DOMAIN_ID_COMMON = 0;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=8)
     */
    protected $type;

    /**
     * @param string $name
     * @param \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null $value
     * @param int $domainId
     */
    public function __construct($name, $value, $domainId)
    {
        $this->name = $name;
        $this->setValue($value);
        $this->domainId = $domainId;
    }

    /**
     * @param \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null $value
     */
    public function edit($value)
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null
     */
    public function getValue()
    {
        if ($this->value === null && $this->type !== self::TYPE_NULL) {
            $message = 'Setting value type "' . $this->type . '" does not allow null value.';
            throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueTypeNotMatchValueException($message);
        }

        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int)$this->value;
            case self::TYPE_FLOAT:
                return (float)$this->value;
            case self::TYPE_BOOLEAN:
                return $this->value === self::BOOLEAN_TRUE;
            case self::TYPE_DATETIME:
                return DateTimeHelper::createFromFormat(self::DATETIME_STORED_FORMAT, $this->value);
            case self::TYPE_MONEY:
                return Money::create($this->value);
            default:
                return $this->value;
        }
    }

    /**
     * @return int|null
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null $value
     */
    protected function setValue($value)
    {
        $this->type = $this->getValueType($value);
        if ($this->type === self::TYPE_BOOLEAN) {
            $this->value = $value === true ? self::BOOLEAN_TRUE : self::BOOLEAN_FALSE;
        } elseif ($this->type === self::TYPE_NULL) {
            $this->value = $value;
        } elseif ($this->type === self::TYPE_DATETIME) {
            $this->value = $value->format(self::DATETIME_STORED_FORMAT);
        } elseif ($this->type === self::TYPE_MONEY) {
            $this->value = $value->getAmount();
        } else {
            $this->value = (string)$value;
        }
    }

    /**
     * @param \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null $value
     * @return string
     */
    protected function getValueType($value)
    {
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
        } elseif ($value instanceof DateTime) {
            return self::TYPE_DATETIME;
        } elseif ($value instanceof Money) {
            return self::TYPE_MONEY;
        }

        $message = sprintf('Setting value type of "%s" is unsupported.', \is_object($value) ? \get_class($value) : \gettype($value))
            . sprintf(' Supported is "%s", "%s", string, integer, float, boolean or null.', DateTime::class, Money::class);
        throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\InvalidArgumentException($message);
    }
}
