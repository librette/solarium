<?php declare(strict_types = 1);

namespace Librette\Solarium\Specification;

use Solarium\QueryType\Select\Query\Query;

interface ISpecification
{

	public function match(Query $query): void;


	public function modifyQuery(Query $query): void;
}
