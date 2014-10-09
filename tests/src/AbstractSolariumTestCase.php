<?php
namespace LibretteTests\Solarium;

use Solarium\Client;
use Tester\TestCase;

/**
 * @author David Matejka
 */
abstract class AbstractSolariumTestCase extends TestCase
{

	/** @var Client */
	protected $client;


	public function setUp()
	{

		$config = include __DIR__ . '/../config/config.php';
		$this->client = new Client($config);
	}
}
