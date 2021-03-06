<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;
use Solarium\Core\Query\Helper;


class Parametrized implements IExpression
{
	use SmartObject;

	/** @var string */
	protected $query;

	/** @var array */
	protected $parameters;


	function __construct(string $query, array $parameters = [])
	{
		$this->query = $query;
		$this->parameters = $parameters;
	}


	public function build(): string
	{
		if (!empty($this->parameters)) {
			$helper = new Helper();

			return $helper->assemble($this->query, $this->parameters);
		}

		return $this->query;
	}

}
