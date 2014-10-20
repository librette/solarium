<?php
namespace Librette\Solarium\Expressions;

use Nette\Object;

/**
 * @author David Matejka
 */
class Range extends Object implements IExpression
{

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
	function __construct($from, $to, $precision = 2)
	{
		$this->from = $from === NULL ? self::ANY : $from;
		$this->to = $to === NULL ? self::ANY : $to;
		$this->precision = $precision;
	}


	public function build()
	{
		return sprintf('[%s TO %s]', $this->formatValue($this->from), $this->formatValue($this->to));
	}


	/**
	 * @param float|int|string $value
	 * @return string
	 */
	private function formatValue($value)
	{
		return is_float($value) ? number_format($value, $this->precision, '.', '') : (string) $value;
	}

}
