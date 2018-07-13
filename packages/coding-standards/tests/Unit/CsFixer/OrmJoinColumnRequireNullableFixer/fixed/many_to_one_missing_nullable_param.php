<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false, name="std_id", referencedColumnName="id")
     */
    private $foo3;
}
