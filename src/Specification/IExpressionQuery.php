<?php
namespace Librette\Solarium\Specification;

use Librette\Solarium\Expressions\IExpression;

/**
 * @author David Matejka
 */
interface IExpressionQuery
{

	/**
	 * @return IExpression
	 */
	public function getExpression();

}