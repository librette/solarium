<?php
namespace Librette\Solarium;

use Nette\Object;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

/**
 * @author David Matejka
 */
abstract class QueryObject extends Object implements IQuery
{

	abstract protected function doPrepareQuery(SelectQuery $query);


	public function fetch(IQueryable $queryable)
	{
		$select = $queryable->createQuery(Client::QUERY_SELECT);
		$this->doPrepareQuery($select);

		return new ResultSet($select, $queryable, $this);
	}

}
