<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;

abstract class Composite implements IExpression
{
	use SmartObject;

	/** @var IExpression[] */
	protected $args;


	/**
	 * @param IExpression|IExpression[]
	 * @param ...
	 */
	final public function __construct($args)
	{
		$this->args = is_array($args) ? $args : func_get_args();
	}


	public function build(): string
	{
		return '(' . implode($this->getSeparator(), array_map(function (IExpression $expression) {
				return $expression->build();
			}, $this->args)) . ')';
	}


	protected abstract function getSeparator(): string;


	public static function fromArray(array $data, ?callable $formatter = null): self
	{
		if ($formatter == null) {
			$formatter = function ($val) {
				if (!$val instanceof IExpression) {
					$val = new Literal($val);
				}

				return $val;
			};
		}

		return new static(array_map(function ($val) use ($formatter) {
			return call_user_func($formatter, $val);
		}, $data));
	}
}
