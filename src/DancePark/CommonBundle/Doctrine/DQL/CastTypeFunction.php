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
class CastTypeFunction extends FunctionNode
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

        $this->field = $parser->InstanceOfParameter();

        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_AS);

        $parser->match(Lexer::T_IDENTIFIER);
        $this->type = $lexer->token;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Retutn sql interpretation of Rand function
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('CAST(%s AS %s)', $this->field->dispatch($sqlWalker), $this->type['value']);
    }
}