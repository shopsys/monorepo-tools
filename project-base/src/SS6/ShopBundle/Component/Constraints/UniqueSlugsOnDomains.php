<?php

namespace SS6\ShopBundle\Component\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueSlugsOnDomains extends Constraint {

	public $message = 'Adresa {{ url }} již existuje.';
	public $messageDuplicate = 'Adresa {{ url }} může být zadána pouze jednou.';

}
