<?php

namespace Shopsys\FrameworkBundle\Component\Javascript\Compiler;

use PLUG\JavaScript\JNodes\nonterminal\JProgramNode;

interface JsCompilerPassInterface
{
    /**
     * @param \PLUG\JavaScript\JNodes\nonterminal\JProgramNode $node
     */
    public function process(JProgramNode $node);
}
