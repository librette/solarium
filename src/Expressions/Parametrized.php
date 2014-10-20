<?php
namespace Librette\Solarium\Expressions;

use Nette\Object;
use Solarium\Core\Query\Helper;

/**
 * @author David Matejka
 */
class Parametrized extends Object implements IExpression
{

	/** @var string */
	protected $query;

	/** @var array */
	protected $parameters;


	/**
	 * @param string
	 * @param array
	 */
	function __construct($query, $parameters = [])
	{
		$this->query = $query;
		$this->parameters = $parameters;
	}


	public function build()
	{
		if (!empty($this->parameters)) {
			$helper = new Helper();

			return $helper->assemble($this->query, $this->parameters);
		}

		return $this->query;
	}

}
