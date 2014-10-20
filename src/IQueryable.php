<?php
namespace Librette\Solarium;

use Solarium\Core\Client\Endpoint;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;

/**
 * @author David Matejka
 */
interface IQueryable
{

	/**
	 * @param string query type
	 * @param mixed options
	 * @return QueryInterface
	 */
	public function createQuery($type, $options = NULL);


	/**
	 * @param QueryInterface
	 * @param Endpoint|string|null
	 * @return ResultInterface
	 */
	public function execute(QueryInterface $query, $endpoint = NULL);

}
