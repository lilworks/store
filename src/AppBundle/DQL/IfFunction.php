<?php
namespace AppBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class IfFunction extends FunctionNode
{
    private $expr = array();

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->expr[] = $parser->ConditionalExpression();

        for ($i = 0; $i < 2; $i++)
        {
            $parser->match(Lexer::T_COMMA);
            $this->expr[] = $parser->ArithmeticExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf('IF(%s, %s, %s)',
            $sqlWalker->walkConditionalExpression($this->expr[0]),
            $sqlWalker->walkArithmeticPrimary($this->expr[1]),
            $sqlWalker->walkArithmeticPrimary($this->expr[2]));
    }
}