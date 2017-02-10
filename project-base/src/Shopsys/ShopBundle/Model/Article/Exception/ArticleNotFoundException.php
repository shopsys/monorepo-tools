<?php

namespace Shopsys\ShopBundle\Model\Article\Exception;

use Shopsys\ShopBundle\Model\Article\Exception\ArticleException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleNotFoundException extends NotFoundHttpException implements ArticleException
{
}
