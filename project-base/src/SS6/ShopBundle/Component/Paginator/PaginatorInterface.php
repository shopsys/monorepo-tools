<?php

namespace SS6\ShopBundle\Component\Paginator;

interface PaginatorInterface {
	public function getResult($page, $limit);
	public function getTotalCount();
}
