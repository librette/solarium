<?php
namespace Librette\Solarium;

use Solarium\Client as BaseClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author David Matejka
 */
class Client extends BaseClient implements IQueryable
{

	public function __construct($options = NULL, EventDispatcherInterface $eventDispatcher = NULL)
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
