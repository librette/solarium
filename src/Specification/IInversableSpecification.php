<?php declare(strict_types = 1);

namespace Librette\Solarium\Specification;

interface IInversableSpecification
{
	public function inverse(bool $inverse = true): void;
}
