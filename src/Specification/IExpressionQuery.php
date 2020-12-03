<?php declare(strict_types = 1);

namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;

interface IExpressionQuery
{
	public function getExpression(): IExpression;
}
