<?php

namespace Librette\Solarium;

use Solarium\QueryType\Select\Query\Query;

class FacetHelpers
{

	const GLOBAL_SUFFIX = '_global';
	const NO_EXCLUDE_SUFFIX = '_no_exclude';


	private function __construct()
	{

	}


	public static function addFacet(Query $query, $key)
	{
		$fieldFacet = $query->getFacetSet()->createFacetField($key);

		return $fieldFacet->setField($key)->addExclude($key);
	}


	public static function addNoFilterFacet(Query $query, $key)
	{
		$queryFacet = $query->getFacetSet()->createFacetQuery(['query' => '*:*', 'key' => $key . self::GLOBAL_SUFFIX]);

		return $queryFacet->addExclude($key);
	}


	public static function addNoExcludeFacet(Query $query, $key)
	{
		return $query->getFacetSet()->createFacetField(['field' => $key, 'key' => $key . self::NO_EXCLUDE_SUFFIX]);
	}
}
