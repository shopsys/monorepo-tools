<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     */
    private $foo2;
}
