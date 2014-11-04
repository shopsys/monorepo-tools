<?php

namespace SS6\ShopBundle\Component\Validator;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Auto extends Annotation {

	public $entity;

}
