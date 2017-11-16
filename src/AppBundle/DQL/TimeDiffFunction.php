<?php
namespace AppBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
* Class DateFormatFunction
*
* Adds the hability to use the MySQL DATE_FORMAT function inside Doctrine
*
* @package Vf\Bundle\VouchedforBundle\DQL
*/
class TimeDiffFunction extends FunctionNode
{

// (1)
    public $firstDateExpression = null;
    public $secondDateExpression = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // (2)
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // (3)
        $this->firstDateExpression = $parser->ArithmeticPrimary(); // (4)
        $parser->match(Lexer::T_COMMA); // (5)
        $this->secondDateExpression = $parser->ArithmeticPrimary(); // (6)
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // (3)
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'TIMEDIFF(' .
        $this->firstDateExpression->dispatch($sqlWalker) . ', ' .
        $this->secondDateExpression->dispatch($sqlWalker) .
        ')'; // (7)
    }
}

