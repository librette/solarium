<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;


interface IExpression
{
	public function build(): string;
}
