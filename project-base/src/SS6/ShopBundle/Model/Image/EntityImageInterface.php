<?php

namespace SS6\ShopBundle\Model\Image;

interface EntityImageInterface {

	/**
	 * @return \SS6\ShopBundle\Model\Image\ImageFileCollection
	 */
	public function getImageFileCollection();
}
