<?php
namespace Librette\Solarium\Specification;

/**
 * @author David Matejka
 */
interface IInversableSpecification
{

	/**
	 * @param bool
	 * @return void
	 */
	public function inverse($inverse = TRUE);
}
