<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use IteratorAggregate;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Symfony\Component\Form\DataTransformerInterface;

class ImagesIdsToImagesTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    public function __construct(ImageFacade $imageRepository)
    {
        $this->imageFacade = $imageRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image[]|null $images
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
     * @return \Shopsys\ShopBundle\Component\Image\Image[]|null
     */
    public function reverseTransform($imagesIds)
    {
        $images = [];

        if (is_array($imagesIds)) {
            foreach ($imagesIds as $imageId) {
                try {
                    $images[] = $this->imageFacade->getById($imageId);
                } catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException $e) {
                    throw new \Symfony\Component\Form\Exception\TransformationFailedException('Image not found', null, $e);
                }
            }
        }

        return $images;
    }
}
