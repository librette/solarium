<?php

namespace Librette\Solarium;

use Solarium\QueryType\Select\Query\Query as SelectQuery;

interface IQueryModifier
{

	/**
	 * @param SelectQuery
	 */
	public function modifyQuery(SelectQuery $query);
}
