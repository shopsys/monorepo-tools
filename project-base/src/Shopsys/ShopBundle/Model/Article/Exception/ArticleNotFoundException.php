<?php

namespace Shopsys\ShopBundle\Model\Article\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleNotFoundException extends NotFoundHttpException implements ArticleException
{
}
