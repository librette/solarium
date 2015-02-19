<?php
namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\FieldExpression;
use Librette\Solarium\Expressions\IExpression;
use Nette\Object;
use Solarium\QueryType\Select\Query\Query;

/**
 * @author David Matejka
 */
class MainQuery extends Object implements ISpecification, IExpressionQuery
{

	/** @var IExpression|null */
	protected $expression;


	/**
	 * @param IExpression
	 */
	public function __construct(/* IExpression */ $expression = NULL)
	{
		if (func_num_args() > 1 && ($expr = func_get_arg(1)) instanceof IExpression) {
			trigger_error('Passing field name as a first argument is deprecated. Pass FieldExpression directly.', E_USER_DEPRECATED);
			$this->expression = new FieldExpression($expression, $expr);
		} else {
			$this->expression = $expression;
		}
	}


	/**
	 * @return IExpression|null
	 */
	public function getExpression()
	{
		return $this->expression;
	}


	public function match(Query $query)
	{
		if ($this->expression && ($queryString = $this->expression->build()) !== NULL) {
			$query->setQuery($queryString);
		}
	}


	public function modifyQuery(Query $query)
	{

	}

}
