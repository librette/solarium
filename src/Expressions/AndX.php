<?php
namespace Librette\Solarium\Expressions;

/**
 * @author David Matejka
 */
class AndX extends Composite
{

	protected function getSeparator()
	{
		return ' AND ';
	}

}
