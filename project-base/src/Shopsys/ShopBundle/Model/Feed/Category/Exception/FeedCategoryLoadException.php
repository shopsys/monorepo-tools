<?php

namespace Shopsys\ShopBundle\Model\Feed\Category\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Feed\Category\Exception\FeedCategoryException;

class FeedCategoryLoadException extends Exception implements FeedCategoryException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
