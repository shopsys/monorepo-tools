<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class CollateFunction extends FunctionNode {

    /**
     * @var \Doctrine\ORM\Query\AST\Node
     */
    private $inputStringExpression;

    /**
     * @var string
     */
    private $collation;

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->inputStringExpression = $parser->StringExpression();
        $parser->match(Lexer::T_COMMA);
        $parser->match(Lexer::T_STRING);
        $this->collation = $parser->getLexer()->token['value'];
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker) {
        return sprintf(
            '%s COLLATE %s',
            $this->inputStringExpression->dispatch($sqlWalker),
            $sqlWalker->getConnection()->quoteIdentifier($this->collation)
        );
    }

}
