<?php

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


	/**
	 * @return string
	 */
	public function build()
	{
		return '(' . implode($this->getSeparator(), array_map(function (IExpression $expression) {
				return $expression->build();
			}, $this->args)) . ')';
	}


	/**
	 * @return string
	 */
	protected abstract function getSeparator();


	/**
	 * @param array
	 * @param callable
	 * @return self
	 */
	public static function fromArray($data, $formatter = null)
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
