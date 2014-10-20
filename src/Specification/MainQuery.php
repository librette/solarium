<?php
namespace Librette\Solarium\Specification;

use Solarium\QueryType\Select\Query\Query;

/**
 * @author David Matejka
 */
class MainQuery extends ExpressionQuery
{

	public function match(Query $query)
	{
		if (($queryString = $this->build()) !== NULL) {
			$query->setQuery($queryString);
		}
	}


	public function modifyQuery(Query $query)
	{

	}

}
