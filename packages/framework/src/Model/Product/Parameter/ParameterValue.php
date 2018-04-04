<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="parameter_values")
 * @ORM\Entity
 */
class ParameterValue
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    protected $text;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $locale;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData $parameterData
     */
    public function __construct(ParameterValueData $parameterData)
    {
        $this->text = $parameterData->text;
        $this->locale = $parameterData->locale;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData $parameterData
     */
    public function edit(ParameterValueData $parameterData)
    {
        $this->text = $parameterData->text;
    }
}
