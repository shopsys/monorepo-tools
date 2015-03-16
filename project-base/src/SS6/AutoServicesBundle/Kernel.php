<?php

namespace SS6\AutoServicesBundle;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel {

	/**
	 * {@inheritdoc}
	 */
	public function getContainer() {
		return parent::getContainer()->get('ss6.auto_services.auto_container');
	}
}
