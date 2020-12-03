<?php declare(strict_types = 1);

namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;
use Librette\Solarium\Expressions\Not;

trait TInversable
{

	/** @var bool */
	protected $inversed = false;


	public function inverse(bool $inverse = true): void
	{
		$this->inversed = $inverse;
	}


	public function isInversed(): bool
	{
		return $this->inversed;
	}


	protected function doInverse(IExpression $expression): IExpression
	{
		if ($this->isInversed()) {
			return $this->_doInverse($expression);
		}

		return $expression;
	}


	protected function _doInverse(IExpression $expression): IExpression
	{
		if ($expression instanceof Not) {
			return $expression->getExpression();
		}

		return new Not($expression);
	}
}
