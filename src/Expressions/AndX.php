<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

class AndX extends Composite
{
	protected function getSeparator(): string
	{
		return ' AND ';
	}
}
