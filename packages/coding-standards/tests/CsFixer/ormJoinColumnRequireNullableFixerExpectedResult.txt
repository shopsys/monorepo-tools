<?php

class Bar
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var \StdObject
     * @ORM\OneToMany(targetEntity="StdObject")
     */
    private $foo1;

    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foo2;

    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false, name="std_id", referencedColumnName="id")
     */
    private $foo3;

    /**
     * @var \StdObject
     * @ORM\ManyToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(name="std_id", referencedColumnName="id", nullable=true)
     */
    private $foo4;

    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false)
     */
    private $foo5;

    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(nullable=false, name="std_id", referencedColumnName="id")
     */
    private $foo6;

    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(name="std_id", referencedColumnName="id", nullable=true)
     */
    private $foo7;

    /**
     * @var \StdObject
     * @ORM\OneToOne(targetEntity="StdObject")
     * @ORM\JoinColumn(
     *     nullable=false,
     *     name="std_id",
     *     referencedColumnName="id"
     * )
     */
    private $foo8;

}
