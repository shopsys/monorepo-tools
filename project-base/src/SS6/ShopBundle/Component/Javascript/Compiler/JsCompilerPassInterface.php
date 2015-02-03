<?php

namespace SS6\ShopBundle\Component\Javascript\Compiler;

import('PLUG.JavaScript.JNodes.nonterminal.JProgramNode');

use JProgramNode;

interface JsCompilerPassInterface {

	public function process(JProgramNode $node);

}
