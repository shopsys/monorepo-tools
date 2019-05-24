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
    /** @access protected */
    const DATETIME_STORED_FORMAT = DateTime::ISO8601;

    /** @access protected */
    const TYPE_STRING = 'string';
    /** @access protected */
    const TYPE_INTEGER = 'integer';
    /** @access protected */
    const TYPE_FLOAT = 'float';
    /** @access protected */
    const TYPE_BOOLEAN = 'boolean';
    /** @access protected */
    const TYPE_DATETIME = 'datetime';
    /** @access protected */
    const TYPE_MONEY = 'money';
    /** @access protected */
    const TYPE_NULL = 'none';

    /** @access protected */
    const BOOLEAN_TRUE = 'true';
    /** @access protected */
    const BOOLEAN_FALSE = 'false';

    public const DOMAIN_ID_COMMON = 0;

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
        if ($this->value === null && $this->type !== static::TYPE_NULL) {
            $message = 'Setting value type "' . $this->type . '" does not allow null value.';
            throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\SettingValueTypeNotMatchValueException($message);
        }

        switch ($this->type) {
            case static::TYPE_INTEGER:
                return (int)$this->value;
            case static::TYPE_FLOAT:
                return (float)$this->value;
            case static::TYPE_BOOLEAN:
                return $this->value === static::BOOLEAN_TRUE;
            case static::TYPE_DATETIME:
                return DateTimeHelper::createFromFormat(static::DATETIME_STORED_FORMAT, $this->value);
            case static::TYPE_MONEY:
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
        if ($this->type === static::TYPE_BOOLEAN) {
            $this->value = $value === true ? static::BOOLEAN_TRUE : static::BOOLEAN_FALSE;
        } elseif ($this->type === static::TYPE_NULL) {
            $this->value = $value;
        } elseif ($this->type === static::TYPE_DATETIME) {
            $this->value = $value->format(static::DATETIME_STORED_FORMAT);
        } elseif ($this->type === static::TYPE_MONEY) {
            $this->value = $value->getAmount();
        } else {
            $this->value = (string)$value;
        }
    }

    /**
     * @param \DateTime|\Shopsys\FrameworkBundle\Component\Money\Money|string|int|float|bool|null|mixed $value
     * @return string
     */
    protected function getValueType($value)
    {
        if (is_int($value)) {
            return static::TYPE_INTEGER;
        } elseif (is_float($value)) {
            return static::TYPE_FLOAT;
        } elseif (is_bool($value)) {
            return static::TYPE_BOOLEAN;
        } elseif (is_string($value)) {
            return static::TYPE_STRING;
        } elseif (is_null($value)) {
            return static::TYPE_NULL;
        } elseif ($value instanceof DateTime) {
            return static::TYPE_DATETIME;
        } elseif ($value instanceof Money) {
            return static::TYPE_MONEY;
        }

        $message = sprintf('Setting value type of "%s" is unsupported.', \is_object($value) ? \get_class($value) : \gettype($value))
            . sprintf(' Supported is "%s", "%s", string, integer, float, boolean or null.', DateTime::class, Money::class);
        throw new \Shopsys\FrameworkBundle\Component\Setting\Exception\InvalidArgumentException($message);
    }
}
