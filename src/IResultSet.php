<?php

namespace Librette\Solarium;

use Nette\Utils\Paginator;


interface IResultSet extends \Traversable, \Countable
{

	/**
	 * @param Paginator
	 * @throws InvalidStateException
	 * @return self
	 */
	public function applyPaginator(Paginator $paginator);


	/**
	 * @param int
	 * @param int
	 * @throws InvalidStateException
	 * @return self
	 */
	public function applyPaging($offset, $limit);


	/**
	 * @throws InvalidStateException
	 * @return self
	 */
	public function clearSorting();


	/**
	 * @param array
	 * @throws InvalidStateException
	 * @return self
	 */
	public function applySorting($sorting);


	/**
	 * @return int
	 */
	public function getTotalCount();

}
