<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foo5;
}
