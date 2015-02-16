<?php
namespace Librette\Solarium\Expressions;

use Nette\Object;

/**
 * @author David Matejka
 */
class FieldExpression extends Object implements IExpression
{

	/** @var string */
	protected $field;

	/** @var IExpression */
	protected $expression;


	/**
	 * @param string
	 * @param IExpression|string
	 */
	public function __construct($field, $expression)
	{
		$this->field = $field;
		$this->expression = $expression instanceof IExpression ? $expression : new Literal($expression);
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
	public function getField()
	{
		return $this->field;
	}


	/**
	 * @return string
	 */
	public function build()
	{
		return sprintf('%s:(%s)', $this->field, $this->expression->build());
	}

}
