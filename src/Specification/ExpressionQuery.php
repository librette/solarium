<?php
namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;
use Nette\Object;

/**
 * @author David Matejka
 */
abstract class ExpressionQuery extends Object implements ISpecification
{

	/** @var string */
	protected $key;

	/** @var IExpression|null */
	protected $expression;


	/**
	 * @param string
	 * @param IExpression
	 */
	public function __construct($key, IExpression $expression = NULL)
	{
		$this->key = $key;
		$this->expression = $expression;
	}


	/**
	 * @return IExpression
	 */
	public function getExpression()
	{
		return $this->expression;
	}


	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}


	protected function build()
	{
		if ($this->expression === NULL) {
			return NULL;
		}

		return sprintf('%s:(%s)', $this->key, $this->expression->build());
	}

}
