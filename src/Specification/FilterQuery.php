<?php
namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\FieldExpression;
use Librette\Solarium\Expressions\IExpression;
use Nette\Object;
use Solarium\QueryType\Select\Query\Query;

/**
 * @author David Matejka
 */
class FilterQuery extends Object implements IInversableSpecification, ISpecification, IExpressionQuery
{

	use TInversable;

	/** @var string */
	private $key;

	/** @var IExpression */
	private $expression;


	/**
	 * @param IExpression
	 * @param string
	 */
	public function __construct(/* IExpression */ $expression, $key = NULL)
	{
		if (is_string($expression) && $key instanceof IExpression) {
			trigger_error('Passing field name as a first argument is deprecated. Pass FieldExpression directly.', E_USER_DEPRECATED);
			$this->expression = new FieldExpression($this->key = $expression, $key);
		} else {
			$this->expression = $expression;
			$this->key = $key;
		}
	}


	/**
	 * @return IExpression
	 */
	public function getExpression()
	{
		return $this->expression;
	}


	public function match(Query $query)
	{
		if (($fq = $this->createFilterQuery($query)) !== NULL) {
			$query->addFilterQuery($fq);
		}
	}


	public function modifyQuery(Query $query)
	{

	}


	protected function createFilterQuery(Query $query)
	{
		if ($this->expression === NULL) {
			return NULL;
		}

		$this->expression = $this->doInverse($this->expression);
		if (($queryString = $this->expression->build()) !== NULL) {
			$key = $this->key ?: md5($queryString);
			$fq = $query->createFilterQuery($key)->setQuery($queryString);
			if ($this->expression instanceof FieldExpression) {
				$fq->addTag($this->expression->getField());
			}
			if ($this->key !== NULL) {
				$fq->addTag($this->key);
			}
		} else {
			$fq = NULL;
		}
		$this->expression = $this->doInverse($this->expression); //return to original state

		return $fq;
	}

}
