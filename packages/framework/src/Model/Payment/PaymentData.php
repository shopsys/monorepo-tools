<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class PaymentData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public $vat;

    /**
     * @var string[]
     */
    public $description;

    /**
     * @var string[]
     */
    public $instructions;

    /**
     * @var int
     */
    public $hidden;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $image;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport[]
     */
    public $transports;

    /**
     * @var bool
     */
    public $czkRounding;

    /**
     * @var string[]
     */
    public $pricesByCurrencyId;

    /**
     * @var bool[]
     */
    public $enabled;

    /**
     * @param string[] $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat|null $vat
     * @param string[] $description
     * @param string[] $instructions
     * @param bool $hidden
     * @param bool[] $enabled
     * @param bool $czkRounding
     * @param array $pricesByCurrencyId
     */
    public function __construct(
        array $name = [],
        Vat $vat = null,
        array $description = [],
        array $instructions = [],
        $hidden = false,
        array $enabled = [],
        $czkRounding = false,
        array $pricesByCurrencyId = []
    ) {
        $this->name = $name;
        $this->vat = $vat;
        $this->description = $description;
        $this->instructions = $instructions;
        $this->hidden = $hidden;
        $this->enabled = $enabled;
        $this->image = new ImageUploadData();
        $this->transports = [];
        $this->czkRounding = $czkRounding;
        $this->pricesByCurrencyId = $pricesByCurrencyId;
    }
}
