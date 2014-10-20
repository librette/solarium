<?php
namespace Librette\Solarium\Expressions;

use Nette\Object;

/**
 * @author David Matejka
 */
class Not extends Object implements IExpression
{

	/** @var IExpression */
	protected $expression;


	/**
	 * @param IExpression $expression
	 */
	public function __construct(IExpression $expression)
	{
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
	public function build()
	{
		return 'NOT(' . $this->expression->build() . ')';
	}

}
