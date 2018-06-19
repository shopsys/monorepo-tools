<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class TransportData
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
     * @var bool
     */
    public $hidden;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $image;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment[]
     */
    public $payments;

    /**
     * @var string[]
     */
    public $pricesByCurrencyId;

    /**
     * @var bool[]
     */
    public $enabled;

    /**
     * @param string[] $names
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat|null $vat
     * @param string[] $descriptions
     * @param string[] $instructions
     * @param bool $hidden
     * @param bool[] $enabled
     * @param string[] $pricesByCurrencyId
     */
    public function __construct(
        array $names = [],
        Vat $vat = null,
        array $descriptions = [],
        array $instructions = [],
        $hidden = false,
        array $enabled = [],
        array $pricesByCurrencyId = []
    ) {
        $this->name = $names;
        $this->vat = $vat;
        $this->description = $descriptions;
        $this->instructions = $instructions;
        $this->hidden = $hidden;
        $this->enabled = $enabled;
        $this->image = new ImageUploadData();
        $this->pricesByCurrencyId = $pricesByCurrencyId;
        $this->payments = [];
    }
}
