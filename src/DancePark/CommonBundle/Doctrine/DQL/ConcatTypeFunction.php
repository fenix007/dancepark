<?php
namespace DancePark\CommonBundle\Doctrine\DQL;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\InputParameter;

/**
 *
 */
class ConcatTypeFunction extends FunctionNode
{
    protected $type;

    /** @var $field InputParameter */
    protected $field;

    /**
     * {@inheritDoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_STRING);
        $this->type = $lexer->token;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Retutn sql interpretation of Rand function
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('Concat(%s)', $this->field->dispatch($sqlWalker), $this->type['value']);
    }
}