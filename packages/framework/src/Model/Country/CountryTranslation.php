<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Country;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="country_translations")
 * @ORM\Entity
 */
class CountryTranslation extends AbstractTranslation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Country
     *
     * @Prezent\Translatable(targetEntity="\Shopsys\FrameworkBundle\Model\Country\Country")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
