<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;


class FieldExpression implements IExpression
{
	use SmartObject;

	/** @var string */
	protected $field;

	/** @var IExpression */
	protected $expression;


	public function __construct(string $field, $expression)
	{
		$this->field = $field;
		$this->expression = $expression instanceof IExpression ? $expression : new Literal($expression);
	}


	public function getExpression(): IExpression
	{
		return $this->expression;
	}


	public function getField(): string
	{
		return $this->field;
	}


	public function build(): string
	{
		return sprintf('%s:(%s)', $this->field, $this->expression->build());
	}
}
