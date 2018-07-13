<?php

class Bar
{
    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(
     *     name="std_id",
     *     referencedColumnName="id"
     * )
     */
    private $foo8;
}
