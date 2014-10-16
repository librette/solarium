<?php
namespace Librette\Solarium\QueryType\DataImport;

use Solarium\Core\Query\Query as BaseQuery;

/**
 * @author David Matejka
 */
class Query extends BaseQuery
{

	const QUERY_DATAIMPORT = 'dataimport';

	const COMMAND_FULL_IMPORT = 'full-import';
	const COMMAND_DELTA_IMPORT = 'delta-import';
	const COMMAND_STATUS = 'status';


	protected $options = [
		'resultclass' => 'Librette\Solarium\QueryType\DataImport\Result',
		'handler'     => 'dataimport',
		'command'     => self::COMMAND_FULL_IMPORT
	];


	/**
	 * Get type for this query
	 *
	 * @return string
	 */
	public function getType()
	{
		return self::QUERY_DATAIMPORT;
	}


	/**
	 * Get the requestbuilder class for this query
	 *
	 * @return RequestBuilder
	 */
	public function getRequestBuilder()
	{
		return new RequestBuilder();
	}


	/**
	 * Get the response parser class for this query
	 *
	 * @return ResponseParser
	 */
	public function getResponseParser()
	{
		return new ResponseParser();
	}


	public function setCommand($command)
	{
		$this->setOption('command', $command);
	}


	public function getCommand()
	{
		return $this->getOption('command');
	}


	public function setClean($clean)
	{
		return $this->setOption('clean', $clean);
	}


	public function getClean()
	{
		return (boolean) $this->getOption('clean');
	}


	public function setCommit($commit)
	{
		return $this->setOption('commit', $commit);
	}


	public function getCommit()
	{
		return (boolean) $this->getOption('commit');
	}


	public function setOptimize($optimize)
	{
		return $this->setOption('optimize', $optimize);
	}


	public function getOptimize()
	{
		return (boolean) $this->getOption('optimize');
	}


	public function getVerbose()
	{
		return FALSE;
	}


	public function getDebug()
	{
		return FALSE;
	}


	public function setEntity($entity)
	{
		return $this->setOption('entity', $entity);
	}


	public function getEntity()
	{
		return $this->getOption('entity');
	}


	public function setCustomParameters(array $parameters)
	{
		return $this->setOption('parameters', $parameters);
	}


	public function setCustomParameter($key, $value)
	{
		$params = $this->getCustomParameters();
		$params[$key] = $value;
		$this->setCustomParameters($params);

		return $this;
	}


	public function getCustomParameters()
	{
		return $this->getOption('parameters') ?: [];
	}

}
