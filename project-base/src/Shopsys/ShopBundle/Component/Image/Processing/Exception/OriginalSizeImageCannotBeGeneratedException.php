<?php

namespace Shopsys\ShopBundle\Component\Image\Processing\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\Processing\Exception\ImageProcessingException;

class OriginalSizeImageCannotBeGeneratedException extends Exception implements ImageProcessingException
{
    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image $image
     * @param \Exception|null $previous
     */
    public function __construct(Image $image, Exception $previous = null)
    {
        $message = 'Original size of ' . $image->getFilename() . ' cannot be resized because it is original uploaded image.';
        parent::__construct($message, 0, $previous);
    }
}
