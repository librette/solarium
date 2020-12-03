<?php declare(strict_types = 1);

namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\FieldExpression;
use Librette\Solarium\Expressions\IExpression;
use Nette\SmartObject;
use Solarium\QueryType\Select\Query\Query;

class FilterQuery implements IInversableSpecification, ISpecification, IExpressionQuery
{
	use SmartObject;

	use TInversable;

	/** @var string */
	private $key;

	/** @var IExpression */
	private $expression;


	/**
	 * @param IExpression
	 * @param string
	 */
	public function __construct(/* IExpression */
		$expression,
		$key = null
	) {
		if (is_string($expression) && $key instanceof IExpression) {
			trigger_error('Passing field name as a first argument is deprecated. Pass FieldExpression directly.', E_USER_DEPRECATED);
			$this->expression = new FieldExpression($this->key = $expression, $key);
		} else {
			$this->expression = $expression;
			$this->key = $key;
		}
	}


	public function getExpression(): IExpression
	{
		return $this->expression;
	}


	public function match(Query $query): void
	{
		$fq = $this->createFilterQuery($query);
		if ($fq !== null) {
			$query->addFilterQuery($fq);
		}
	}


	public function modifyQuery(Query $query): void
	{

	}


	protected function createFilterQuery(Query $query): ?\Solarium\QueryType\Select\Query\FilterQuery
	{
		if ($this->expression === null) {
			return null;
		}

		$this->expression = $this->doInverse($this->expression);
		if (($queryString = $this->expression->build()) !== null) {
			$key = $this->key ?: md5($queryString);
			$fq = $query->createFilterQuery($key)->setQuery($queryString);
			if ($this->expression instanceof FieldExpression) {
				$fq->addTag($this->expression->getField());
			}
			if ($this->key !== null) {
				$fq->addTag($this->key);
			}
		} else {
			$fq = null;
		}
		$this->expression = $this->doInverse($this->expression); //return to original state

		return $fq;
	}

}
