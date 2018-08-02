<?php

namespace Librette\Solarium\Specification;

interface IInversableSpecification
{

	/**
	 * @param bool
	 * @return void
	 */
	public function inverse($inverse = true);
}
