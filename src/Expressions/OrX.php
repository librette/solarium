<?php
namespace Librette\Solarium\Expressions;

/**
 * @author David Matejka
 */
class OrX extends Composite
{

	protected function getSeparator()
	{
		return ' OR ';
	}

}
