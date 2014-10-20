<?php
namespace Librette\Solarium\Expressions;


/**
 * @author David Matejka
 */
interface IExpression
{

	/**
	 * @return string
	 */
	public function build();
}
