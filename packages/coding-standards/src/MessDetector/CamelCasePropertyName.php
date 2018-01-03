<?php

namespace ShopSys\CodingStandards\MessDetector;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class CamelCasePropertyName extends AbstractRule implements ClassAware
{
    /**
     * @param AbstractNode $node
     */
    public function apply(AbstractNode $node)
    {
        $astClass = $node->getNode();
        /* @var $astClass \PDepend\Source\AST\ASTClass */

        foreach ($astClass->getProperties() as $property) {
            $propertyName = $property->getName();
            if (!preg_match('/^\$[a-z][a-zA-Z0-9]*$/', $propertyName)) {
                $this->addViolation($node, [$propertyName]);
            }
        }
    }
}
