<?php

namespace Librette\Solarium\Expressions;

class OrX extends Composite
{

	protected function getSeparator()
	{
		return ' OR ';
	}

}
