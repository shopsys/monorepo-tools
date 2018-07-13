<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foo2;
}
