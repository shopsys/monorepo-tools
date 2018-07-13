<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(name="std_id", referencedColumnName="id", nullable=true)
     */
    private $foo4;
}
