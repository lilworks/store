<?php
namespace AppBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
 *CREATE FUNCTION SPLIT_TONES(   x VARCHAR(255),   delim VARCHAR(1), pos INT )
 * RETURNS VARCHAR(255)
 * RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
 * LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1), delim, '');
 */
class SplitStringFunction extends FunctionNode
{

    public $delimiter;
    public $col ;
    public $pos ;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->col = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->delimiter = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->pos = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'SPLIT_STRING(' .
        $this->col->dispatch($sqlWalker) . ', ' .
        $this->delimiter->dispatch($sqlWalker) . ', ' .
        $this->pos->dispatch($sqlWalker) .
        ')';
    }
}

