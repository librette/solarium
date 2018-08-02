<?php

namespace Librette\Solarium\Expressions;

class AndX extends Composite
{

	protected function getSeparator()
	{
		return ' AND ';
	}

}
