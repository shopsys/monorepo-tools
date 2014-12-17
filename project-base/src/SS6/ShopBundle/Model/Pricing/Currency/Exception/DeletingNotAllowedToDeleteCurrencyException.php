<?php

namespace SS6\ShopBundle\Model\Pricing\Currency\Exception;

use Exception;
use SS6\ShopBundle\Model\Pricing\Currency\Exception\CurrencyException;

class DeletingNotAllowedToDeleteCurrencyException extends Exception implements CurrencyException {

}
