<?php

namespace Librette\Solarium\QueryType\DataImport;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractRequestBuilder;
use Solarium\Core\Query\QueryInterface;

class RequestBuilder extends AbstractRequestBuilder
{

	public function build(QueryInterface $query)
	{
		/** @var Query $query */
		$request = parent::build($query);
		$request->setMethod(Request::METHOD_GET);

		$request->addParam('command', $query->getCommand());
		$request->addParam('clean', $query->getClean());
		$request->addParam('commit', $query->getCommit());
		$request->addParam('optimize', $query->getOptimize());
		$request->addParam('debug', $query->getDebug());
		$request->addParam('verbose', $query->getVerbose());
		if ($query->getEntity()) {
			$request->addParam('entity', $query->getEntity());
		}
		foreach ($query->getCustomParameters() as $key => $value) {
			$request->addParam($key, $value);
		}

		return $request;
	}
}
