<?php

namespace Shopsys\FrameworkBundle\Model\Slider;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class SliderItemFactory implements SliderItemFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Slider\SliderItemData $data
     * @return \Shopsys\FrameworkBundle\Model\Slider\SliderItem
     */
    public function create(SliderItemData $data): SliderItem
    {
        $classData = $this->entityNameResolver->resolve(SliderItem::class);

        return new $classData($data);
    }
}
