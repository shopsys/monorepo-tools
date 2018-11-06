<?php

namespace Shopsys\FrameworkBundle\Component\Paginator;

interface PaginatorInterface
{
    /**
     * @param mixed $page
     * @param mixed $pageSize
     */
    public function getResult($page, $pageSize);

    public function getTotalCount();
}
