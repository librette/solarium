<?php
namespace Librette\Solarium\QueryType\DataImport;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * @author David Matejka
 */
class Result extends BaseResult
{

	/** @var string */
	protected $status;

	/** @var array */
	protected $statusMessages;


	/**
	 * @return string
	 */
	public function getStatus()
	{
		$this->parseResponse();

		return $this->status;
	}


	/**
	 * @return array
	 */
	public function getStatusMessages()
	{
		$this->parseResponse();

		return $this->statusMessages;
	}

}
