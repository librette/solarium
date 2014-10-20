<?php
namespace Librette\Solarium\Specification;

use Solarium\QueryType\Select\Query\Query;

/**
 * @author David Matejka
 */
class FilterQuery extends ExpressionQuery implements IInversableSpecification
{

	use TInversable;


	public function match(Query $query)
	{
		if (($fq = $this->createFilterQuery($query)) !== NULL) {
			$query->addFilterQuery($fq);
		}
	}


	public function modifyQuery(Query $query)
	{

	}


	protected function createFilterQuery(Query $query)
	{
		if ($this->expression === NULL) {
			return NULL;
		}

		$this->expression = $this->doInverse($this->expression);
		if (($queryString = $this->build()) !== NULL) {
			$result = $query->createFilterQuery($this->key)->setQuery($queryString)->addTag($this->key);
		} else {
			$result = NULL;
		}
		$this->expression = $this->doInverse($this->expression); //return to original state

		return $result;
	}

}
