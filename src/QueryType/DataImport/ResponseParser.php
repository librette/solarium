<?php
namespace Librette\Solarium\QueryType\DataImport;

use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;

/**
 * Parse Data import response data
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{

	/**
	 * Get result data for the response
	 *
	 * @param Result
	 * @return array
	 */
	public function parse($result)
	{
		$data = $result->getData();

		$result = $this->addHeaderInfo($data, []);
		$result['status'] = $data['status'];
		$result['statusMessages'] = $data['statusMessages'];

		return $result;
	}

}
