<?php
namespace Librette\Solarium\Expressions;

use Nette\Object;

/**
 * @author David Matejka
 */
class Literal extends Object implements IExpression
{

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
