<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;


class Not implements IExpression
{
	use SmartObject;

	/** @var IExpression */
	protected $expression;


	public function __construct(IExpression $expression)
	{
		$this->expression = $expression;
	}


	public function getExpression(): IExpression
	{
		return $this->expression;
	}


	public function build(): string
	{
		return 'NOT(' . $this->expression->build() . ')';
	}
}
