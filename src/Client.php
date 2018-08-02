<?php

namespace Librette\Solarium;

use Solarium\Client as BaseClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Client extends BaseClient implements IQueryable
{

	public function __construct($options = null, EventDispatcherInterface $eventDispatcher = null)
	{
		parent::__construct($options);
		if ($eventDispatcher) {
			$this->eventDispatcher = $eventDispatcher;
		}
	}


	protected function init()
	{
		$origDispatcher = $this->eventDispatcher;
		parent::init();
		if ($origDispatcher) {
			$this->eventDispatcher = $origDispatcher;
		}
	}


	/**
	 * @param IQuery
	 * @return mixed|ResultSet
	 */
	public function fetch(IQuery $query)
	{
		return $query->fetch($this);
	}

}
