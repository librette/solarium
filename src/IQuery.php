<?php
namespace Librette\Solarium;

/**
 * @author David Matejka
 */
interface IQuery
{

	/**
	 * @param IQueryable
	 * @return \Traversable|IResultSet|mixed
	 */
	public function fetch(IQueryable $queryable);
}

