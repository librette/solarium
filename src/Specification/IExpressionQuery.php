<?php

namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;

interface IExpressionQuery
{

	/**
	 * @return IExpression
	 */
	public function getExpression();

}
