<?php
namespace DancePark\CommonBundle\Doctrine\DQL;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 *
 */
class ExtractTimeFunction extends FunctionNode
{
    protected $type;

    protected $field;

    /**
     * {@inheritDoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $parser->match(Lexer::T_IDENTIFIER);

        $lexer = $parser->getLexer();
        $this->type = $lexer->token['value'];

        $parser->match(Lexer::T_FROM);

        $this->field = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Retutn sql interpretation of Rand function
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('EXTRACT(%s FROM %s)', $this->type, $this->field->dispatch($sqlWalker));
    }
}