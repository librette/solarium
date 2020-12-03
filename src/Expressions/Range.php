<?php declare(strict_types = 1);

namespace Librette\Solarium\Expressions;

use Nette\SmartObject;


class Range implements IExpression
{
	use SmartObject;

	const ANY = '*';

	/** @var float|string */
	protected $from;

	/** @var float|string */
	protected $to;

	/** @var int */
	protected $precision;


	/**
	 * @param string|float|int
	 * @param string|float|int
	 * @param int only for float
	 */
	function __construct($from, $to, int $precision = 2)
	{
		$this->from = $from === null ? self::ANY : $from;
		$this->to = $to === null ? self::ANY : $to;
		$this->precision = $precision;
	}


	public function build(): string
	{
		return sprintf('[%s TO %s]', $this->formatValue($this->from), $this->formatValue($this->to));
	}


	/**
	 * @param float|int|string $value
	 * @return string
	 */
	private function formatValue($value): string
	{
		return is_float($value) ? number_format($value, $this->precision, '.', '') : (string) $value;
	}
}
