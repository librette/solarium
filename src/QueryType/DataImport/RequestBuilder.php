<?php
namespace Librette\Solarium\QueryType\DataImport;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\RequestBuilder as BaseRequestBuilder;

/**
 * @author David Matejka
 */
class RequestBuilder extends BaseRequestBuilder
{

	public function build(QueryInterface $query)
	{

		$request = parent::build($query);
		/** @var Query $dataImportQuery */
		$dataImportQuery = $query;
		$request->setMethod(Request::METHOD_GET);

		$request->addParam('command', $dataImportQuery->getCommand());
		$request->addParam('clean', $dataImportQuery->getClean());
		$request->addParam('commit', $dataImportQuery->getCommit());
		$request->addParam('optimize', $dataImportQuery->getOptimize());
		$request->addParam('debug', $dataImportQuery->getDebug());
		$request->addParam('verbose', $dataImportQuery->getVerbose());
		if ($dataImportQuery->getEntity()) {
			$request->addParam('entity', $dataImportQuery->getEntity());
		}

		return $request;
	}
}
