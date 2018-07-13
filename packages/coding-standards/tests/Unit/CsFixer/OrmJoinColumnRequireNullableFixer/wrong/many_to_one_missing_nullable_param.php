<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(name="std_id", referencedColumnName="id")
     */
    private $foo3;
}
