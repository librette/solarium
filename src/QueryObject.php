<?php

namespace Librette\Solarium;

use Nette\SmartObject;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

abstract class QueryObject implements IQuery
{
	use SmartObject;


	abstract protected function doPrepareQuery(SelectQuery $query);


	public function fetch(IQueryable $queryable)
	{
		$select = $queryable->createQuery(Client::QUERY_SELECT);
		assert($select instanceof SelectQuery);
		$this->doPrepareQuery($select);

		return new ResultSet($select, $queryable, $this);
	}

}
