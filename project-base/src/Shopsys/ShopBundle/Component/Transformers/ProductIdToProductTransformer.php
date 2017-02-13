<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;
use Symfony\Component\Form\DataTransformerInterface;

class ProductIdToProductTransformer implements DataTransformerInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     * @return int|null
     */
    public function transform($product)
    {
        if ($product instanceof Product) {
            return $product->getId();
        }
        return null;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ShopBundle\Model\Product\Product|null
     */
    public function reverseTransform($productId)
    {
        if (empty($productId)) {
            return null;
        }
        try {
            $product = $this->productRepository->getById($productId);
        } catch (\Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
            throw new \Symfony\Component\Form\Exception\TransformationFailedException('Product not found', null, $e);
        }
        return $product;
    }
}
