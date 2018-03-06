<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use Twig_BaseNodeVisitor;
use Twig_Environment;
use Twig_Node;
use Twig_Node_Expression_Filter;

/**
 * Normalizes Twig translation filters by replacing custom filter names "transHtml" and "transChoiceHtml" by the default filter
 * names "trans" and "transChoice". This ensures that they will be treated the same way by following Twig node visitors.
 *
 * Used for dumping translation messages in both custom and default translation filters because the extractor class
 * \JMS\TranslationBundle\Translation\Extractor\File\TwigFileExtractor is not very extensible.
 */
class CustomTransFiltersVisitor extends Twig_BaseNodeVisitor
{
    const CUSTOM_TO_DEFAULT_TRANS_FILTERS_MAP = [
        'transHtml' => 'trans',
        'transchoiceHtml' => 'transchoice',
    ];
    const PRIORITY = -1;

    /**
     * {@inheritdoc}
     */
    protected function doEnterNode(Twig_Node $node, Twig_Environment $env)
    {
        if ($node instanceof Twig_Node_Expression_Filter) {
            $filterNameConstantNode = $node->getNode('filter');
            $filterName = $filterNameConstantNode->getAttribute('value');
            if (array_key_exists($filterName, self::CUSTOM_TO_DEFAULT_TRANS_FILTERS_MAP)) {
                $newFilterName = self::CUSTOM_TO_DEFAULT_TRANS_FILTERS_MAP[$filterName];
                $this->replaceCustomFilterName($node, $newFilterName);
            }
        }

        return $node;
    }

    /**
     * @param \Twig_Node_Expression_Filter $filterExpressionNode
     * @param string $newFilterName
     */
    private function replaceCustomFilterName(Twig_Node_Expression_Filter $filterExpressionNode, $newFilterName)
    {
        $filterNameConstantNode = $filterExpressionNode->getNode('filter');
        $filterNameConstantNode->setAttribute('value', $newFilterName);

        // \Twig_Node_Expression_Filter has "name" attribute only if it is compiled
        if ($filterExpressionNode->hasAttribute('name')) {
            $filterExpressionNode->setAttribute('name', $newFilterName);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doLeaveNode(Twig_Node $node, Twig_Environment $env)
    {
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return self::PRIORITY;
    }
}
