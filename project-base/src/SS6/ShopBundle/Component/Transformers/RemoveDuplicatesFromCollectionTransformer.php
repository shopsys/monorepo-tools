<?php

namespace SS6\ShopBundle\Component\Transformers;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;

class RemoveDuplicatesFromCollectionTransformer implements DataTransformerInterface {

	/**
	 * @param string|null $values
	 * @return string|null
	 */
	public function transform($values) {
		return $values;
	}

	/**
	 * @param mixed $collection
	 * @return mixed
	 */
	public function reverseTransform($collection) {
		if ($collection instanceof Collection) {
			$collectionArray = $collection->toArray();

			$collection->clear();
			foreach ($collectionArray as $key => $value) {
				if (!$collection->contains($value)) {
					$collection->set($key, $value);
				}
			}
		}

		return $collection;
	}
}
