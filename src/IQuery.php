<?php

namespace Librette\Solarium;

interface IQuery
{

	/**
	 * @param IQueryable
	 * @return \Traversable|IResultSet|mixed
	 */
	public function fetch(IQueryable $queryable);
}

