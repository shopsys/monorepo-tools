<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class FieldFunction extends FunctionNode
{
    /**
     * @var \Doctrine\ORM\Query\AST\Node
     */
    private $firstArgumentExpression;

    /**
     * @var \Doctrine\ORM\Query\AST\Node[]
     */
    private $nextArgumentExpressions;

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstArgumentExpression = $parser->ArithmeticExpression();

        $lexer = $parser->getLexer();
        $this->nextArgumentExpressions = [];
        while (Lexer::T_COMMA === $lexer->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->nextArgumentExpressions[] = $parser->ArithmeticPrimary();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $values = array_map(function (Node $argumentExpression) use ($sqlWalker) {
            return $argumentExpression->dispatch($sqlWalker);
        }, $this->nextArgumentExpressions);
        $sql = 'FIELD(' . $this->firstArgumentExpression->dispatch($sqlWalker) . ',ARRAY[' . implode(',', $values) . '])';

        return $sql;
    }
}
