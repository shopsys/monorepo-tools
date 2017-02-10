<?php

namespace Shopsys\ShopBundle\Model\Administrator;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Administrator\Administrator;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrator_grid_limits")
 */
class AdministratorGridLimit
{

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Administrator
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Administrator\Administrator", inversedBy="gridLimits")
     * @ORM\JoinColumn(name="administrator_id", referencedColumnName="id", nullable=false)
     */
    private $administrator;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=128)
     */
    protected $gridId;

    /**
     * @var int
     *
     * @ORM\Column(name="""limit""",type="integer")
     */
    protected $limit;

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @param string $gridId
     * @param int $limit
     */
    public function __construct(Administrator $administrator, $gridId, $limit) {
        $this->administrator = $administrator;
        $this->gridId = $gridId;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getGridId() {
        return $this->gridId;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit) {
        $this->limit = $limit;
    }
}
