<?php

namespace Librette\Solarium\Specification;

use Solarium\QueryType\Select\Query\Query;

interface ISpecification
{

	/**
	 * @param Query
	 */
	public function match(Query $query);


	/**
	 * @param Query
	 */
	public function modifyQuery(Query $query);
}
