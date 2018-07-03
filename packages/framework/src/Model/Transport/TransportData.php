<?php

namespace Shopsys\FrameworkBundle\Model\Transport;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;

class TransportData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat|null
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

    public function __construct()
    {
        $this->name = [];
        $this->description = [];
        $this->instructions = [];
        $this->hidden = false;
        $this->enabled = [];
        $this->image = new ImageUploadData();
        $this->pricesByCurrencyId = [];
        $this->payments = [];
    }
}
