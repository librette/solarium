<?php
namespace LibretteTests\Solarium;

use Librette\Solarium\QueryType\DataImport\Query;
use Nette;
use Nette\Configurator;
use Solarium\Client;
use Solarium\Core\Plugin\Plugin;
use Tester;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author David Matějka
 */
class ExtensionTestCaseTest extends TestCase
{

	public function setUp()
	{

	}


	public function testContainer()
	{
		$container = $this->createContainer(__DIR__ . '/../config/config.neon');
		/** @var Client $client */
		$client = $container->getByType('Solarium\Client');
		Assert::type('Solarium\Client', $client);
		$types = $client->getQueryTypes();
		Assert::true(isset($types[Query::QUERY_DATAIMPORT]));
		$endpoints = $client->getEndpoints();
		Assert::true(isset($endpoints['default']));
		Assert::same('foo', $endpoints['default']->getOption('host'));
		Assert::same(123, $endpoints['default']->getOption('port'));
	}


	public function testMultipleEndpoints()
	{
		$container = $this->createContainer(__DIR__ . '/../config/config.endpoints.neon');
		/** @var Client $client */
		$client = $container->getByType('Solarium\Client');
		Assert::count(2, $client->getEndpoints());
		Assert::same('bar', $client->getEndpoint()->getKey());
		Assert::same('host1', $client->getEndpoint('foo')->getHost());
		Assert::same('host2', $client->getEndpoint('bar')->getHost());
	}


	public function testPlugins()
	{
		$container = $this->createContainer(__DIR__ . '/../config/config.plugins.neon');
		/** @var Client $client */
		$client = $container->getByType('Solarium\Client');
		$foo = $client->getPlugin('foo');
		Assert::type('\LibretteTests\Solarium\PluginMock', $foo);
		Assert::equal([], $foo->getOptions());
		$bar = $client->getPlugin('bar');
		Assert::type('\LibretteTests\Solarium\PluginMock', $bar);
		Assert::equal(['key' => 'value'], $bar->getOptions());
	}


	public function testKdybyEvents()
	{
		$container = $this->createContainer(__DIR__ . '/../config/config.events.neon');
		/** @var Client $client */
		$client = $container->getByType('Solarium\Client');
		$eventDispatcher = $client->getEventDispatcher();
		$symfonyProxy = $container->getService('events.symfonyProxy');
		Assert::same($symfonyProxy, $eventDispatcher);
	}


	private function createContainer($config)
	{
		$configurator = new Configurator();
		$configurator->addParameters(['container' => ['class' => 'Container' . md5($config)]]);
		$configurator->defaultExtensions = array_intersect_key($configurator->defaultExtensions, ['extensions' => TRUE]);
		$configurator->addConfig($config);
		$configurator->setTempDirectory(TEMP_DIR);

		return $configurator->createContainer();
	}
}


class PluginMock extends Plugin
{

}


\run(new ExtensionTestCaseTest());
