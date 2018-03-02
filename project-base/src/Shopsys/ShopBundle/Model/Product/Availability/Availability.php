<?php

namespace Shopsys\FrameworkBundle\Model\Product\Availability;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @ORM\Table(name="availabilities")
 * @ORM\Entity
 */
class Availability extends AbstractTranslatableEntity
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
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityTranslation")
     */
    protected $translations;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dispatchTime;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    public function __construct(AvailabilityData $availabilityData)
    {
        $this->translations = new ArrayCollection();
        $this->setTranslations($availabilityData);
        $this->dispatchTime = $availabilityData->dispatchTime;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $locale
     * @return string
     */
    public function getName($locale = null)
    {
        return $this->translation($locale)->getName();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    private function setTranslations(AvailabilityData $availabilityData)
    {
        foreach ($availabilityData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityTranslation
     */
    protected function createTranslation()
    {
        return new AvailabilityTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityData $availabilityData
     */
    public function edit(AvailabilityData $availabilityData)
    {
        $this->setTranslations($availabilityData);
        $this->dispatchTime = $availabilityData->dispatchTime;
    }

    /**
     * @return int|null
     */
    public function getDispatchTime()
    {
        return $this->dispatchTime;
    }
}
