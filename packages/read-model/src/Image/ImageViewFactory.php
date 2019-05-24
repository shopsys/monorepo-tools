<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Image;

use Shopsys\FrameworkBundle\Component\Image\Image;

/**
 * @experimental
 */
class ImageViewFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @return \Shopsys\ReadModelBundle\Image\ImageView
     */
    public function createFromImage(Image $image): ImageView
    {
        return new ImageView(
            $image->getId(),
            $image->getExtension(),
            $image->getEntityName(),
            $image->getType()
        );
    }
}
