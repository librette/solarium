<?php
namespace Librette\Solarium\Specification;

use Solarium\QueryType\Select\Query\Query;

/**
 * @author David Matejka
 */
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
