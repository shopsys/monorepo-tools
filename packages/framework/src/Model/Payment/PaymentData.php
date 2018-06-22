<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;

class PaymentData
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

    public function __construct()
    {
        $this->name = [];
        $this->description = [];
        $this->instructions = [];
        $this->hidden = false;
        $this->enabled = [];
        $this->image = new ImageUploadData();
        $this->transports = [];
        $this->czkRounding = false;
        $this->pricesByCurrencyId = [];
    }
}
