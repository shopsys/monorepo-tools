<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

use DateTimeInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\Product;

/**
 * @experimental
 */
class ApiProductTransformer
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return array
     */
    public function transform(Product $product): array
    {
        $names = $this->transformNames($product);
        $shortDescriptions = $this->transformShortDescriptions($product);
        $longDescriptions = $this->transformLongDescriptions($product);

        return [
            'uuid' => $product->getUuid(),
            'name' => $names,
            'hidden' => $product->isHidden(),
            'sellingDenied' => $product->getCalculatedSellingDenied(),
            'sellingFrom' => $this->formatDateTime($product->getSellingFrom()),
            'sellingTo' => $this->formatDateTime($product->getSellingTo()),
            'catnum' => $product->getCatnum(),
            'ean' => $product->getEan(),
            'partno' => $product->getPartno(),
            'shortDescription' => $shortDescriptions,
            'longDescription' => $longDescriptions,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string[]
     */
    protected function transformNames(Product $product): array
    {
        $result = [];
        foreach ($this->domain->getAllLocales() as $locale) {
            $result[$locale] = $product->getName($locale);
        }
        return $result;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string[]
     */
    protected function transformShortDescriptions(Product $product): array
    {
        $result = [];
        foreach ($this->domain->getAllIds() as $domainId) {
            $result[$domainId] = $product->getShortDescription($domainId);
        }
        return $result;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string[]
     */
    protected function transformLongDescriptions(Product $product): array
    {
        $result = [];
        foreach ($this->domain->getAllIds() as $domainId) {
            $result[$domainId] = $product->getDescription($domainId);
        }
        return $result;
    }

    /**
     * @param \DateTimeInterface|null $dateTime
     * @return string|null
     */
    protected function formatDateTime(?DateTimeInterface $dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->format(DateTimeInterface::ATOM);
    }
}
