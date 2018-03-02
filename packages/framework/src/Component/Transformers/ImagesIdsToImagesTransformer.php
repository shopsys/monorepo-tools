<?php

namespace Shopsys\FrameworkBundle\Component\Transformers;

use IteratorAggregate;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Symfony\Component\Form\DataTransformerInterface;

class ImagesIdsToImagesTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    public function __construct(ImageFacade $imageRepository)
    {
        $this->imageFacade = $imageRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[]|null $images
     * @return int[]
     */
    public function transform($images)
    {
        $imagesIds = [];

        if (is_array($images) || $images instanceof IteratorAggregate) {
            foreach ($images as $image) {
                $imagesIds[] = $image->getId();
            }
        }

        return $imagesIds;
    }

    /**
     * @param int[] $imagesIds
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]|null
     */
    public function reverseTransform($imagesIds)
    {
        $images = [];

        if (is_array($imagesIds)) {
            foreach ($imagesIds as $imageId) {
                try {
                    $images[] = $this->imageFacade->getById($imageId);
                } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException $e) {
                    throw new \Symfony\Component\Form\Exception\TransformationFailedException('Image not found', null, $e);
                }
            }
        }

        return $images;
    }
}
