<?php

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;


class Literal implements IExpression
{
	use SmartObject;

	/** @var string */
	protected $value;


	/**
	 * @param string $value
	 */
	function __construct($value)
	{
		$this->value = $value;
	}


	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @return string
	 */
	public function build()
	{
		return (string) $this->value;
	}

}
