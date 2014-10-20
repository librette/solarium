<?php
namespace Librette\Solarium;

use Nette\Object;
use Nette\Utils\Paginator;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Result as SelectResult;

/**
 * @author David Matejka
 */
class ResultSet extends Object implements \IteratorAggregate, IResultSet
{

	/** @var IQueryable */
	protected $queryable;

	/** @var SelectQuery */
	protected $selectQuery;

	/** @var SelectResult */
	protected $selectResult;

	/** @var IQuery */
	protected $query;


	/**
	 * @param SelectQuery
	 * @param IQueryable
	 * @param IQuery
	 */
	public function __construct(SelectQuery $selectQuery, IQueryable $queryable, IQuery $query = NULL)
	{
		$this->queryable = $queryable;
		$this->selectQuery = $selectQuery;
		$this->query = $query;
	}


	/**
	 * @param Paginator
	 * @param bool fills paginator "itemCount", if true, solr query will be executed immediately
	 * @throws InvalidStateException
	 */
	public function applyPaginator(Paginator $paginator, $fillItemCount = TRUE)
	{
		$this->applyPaging($paginator->getOffset(), $paginator->getLength());
		if ($fillItemCount) {
			$this->execute();
			$paginator->setItemCount($this->getTotalCount());
		}
	}


	public function applyPaging($offset, $limit)
	{
		$this->ensureNotExecuted();
		$this->selectQuery->setStart($offset)
		                  ->setRows($limit);
	}


	public function clearSorting()
	{
		$this->ensureNotExecuted();
		$this->selectQuery->clearSorts();
	}


	public function applySorting($sorting)
	{
		$this->ensureNotExecuted();
		foreach ($sorting as $field => $order) {
			if (is_numeric($field)) {
				$field = $order;
				$order = 'asc';
			}
			$this->selectQuery->addSort($field, $order);
		}
	}


	public function getTotalCount()
	{
		$this->execute();

		return $this->selectResult->getNumFound();
	}


	public function count()
	{
		$this->execute();

		return $this->selectResult->count();
	}


	public function getIterator()
	{
		$this->execute();

		return new \ArrayIterator($this->selectResult->getDocuments());
	}


	public function getResult()
	{
		$this->execute();

		return $this->selectResult;
	}


	private function execute()
	{
		if ($this->selectResult) {
			return;
		}
		if ($this->query instanceof IQueryModifier) {
			$this->query->modifyQuery($this->selectQuery);
		}
		$this->selectResult = $this->queryable->execute($this->selectQuery);
	}


	private function  ensureNotExecuted()
	{
		if ($this->selectResult) {
			throw new InvalidStateException("You cannot modify result set, that was fetched from the storage.");
		}
	}
}
