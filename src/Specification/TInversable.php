<?php

namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;
use Librette\Solarium\Expressions\Not;

trait TInversable
{

	/** @var bool */
	protected $inversed;


	public function inverse($inverse = true)
	{
		$this->inversed = $inverse;
	}


	/**
	 * @return bool
	 */
	public function isInversed()
	{
		return $this->inversed;
	}


	protected function doInverse(IExpression $expression)
	{
		if ($this->isInversed()) {
			return $this->_doInverse($expression);
		}

		return $expression;
	}


	protected function _doInverse(IExpression $expression)
	{
		if ($expression instanceof Not) {
			return $expression->getExpression();
		}

		return new Not($expression);
	}
}
