<?php

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;


class Literal implements IExpression
{
	use SmartObject;

	/** @var string */
	protected $value;


	function __construct(string $value)
	{
		$this->value = $value;
	}


	public function getValue(): string
	{
		return $this->value;
	}


	public function build(): string
	{
		return (string) $this->value;
	}
}
