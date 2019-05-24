<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Product\Listed;

use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Webmozart\Assert\Assert;

/**
 * @experimental
 *
 * Class representing products in lists in frontend
 *
 * @see https://github.com/shopsys/shopsys/blob/master/docs/model/introduction-to-read-model.md
 */
class ListedProductView
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Shopsys\ReadModelBundle\Image\ImageView|null
     */
    protected $image;

    /**
     * @var string
     */
    protected $availability;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    protected $sellingPrice;

    /**
     * @var string|null
     */
    protected $shortDescription;

    /**
     * @var int[]
     */
    protected $flagIds = [];

    /**
     * @var \Shopsys\ReadModelBundle\Product\Action\ProductActionView
     */
    protected $action;

    /**
     * ListedProductView constructor.
     * @param int $id
     * @param string $name
     * @param string|null $shortDescription
     * @param string $availability
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice $sellingPrice
     * @param int[] $flagIds
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $action
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $image
     */
    public function __construct(
        int $id,
        string $name,
        ?string $shortDescription,
        string $availability,
        ProductPrice $sellingPrice,
        array $flagIds,
        ProductActionView $action,
        ?ImageView $image
    ) {
        Assert::allInteger($flagIds);

        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->availability = $availability;
        $this->sellingPrice = $sellingPrice;
        $this->shortDescription = $shortDescription;
        $this->flagIds = $flagIds;
        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Shopsys\ReadModelBundle\Image\ImageView|null
     */
    public function getImage(): ?ImageView
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getAvailability(): string
    {
        return $this->availability;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    public function getSellingPrice(): ProductPrice
    {
        return $this->sellingPrice;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @return int[]
     */
    public function getFlagIds(): array
    {
        return $this->flagIds;
    }

    /**
     * @return \Shopsys\ReadModelBundle\Product\Action\ProductActionView
     */
    public function getAction(): ProductActionView
    {
        return $this->action;
    }
}
