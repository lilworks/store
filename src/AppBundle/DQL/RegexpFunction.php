<?php
namespace AppBundle\DQL;


use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class RegexpFunction extends FunctionNode {

    public $regexpExpression = null;
    public $valueExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->regexpExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->valueExpression = $parser->StringExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return '(' . $this->valueExpression->dispatch($sqlWalker) . ' REGEXP ' .
        $sqlWalker->walkStringPrimary($this->regexpExpression) . ')';
    }
}