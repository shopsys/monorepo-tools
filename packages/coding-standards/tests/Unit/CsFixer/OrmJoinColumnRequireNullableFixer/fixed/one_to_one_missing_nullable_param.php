<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false, name="std_id", referencedColumnName="id")
     */
    private $foo6;
}
