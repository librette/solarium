<?php

namespace Librette\Solarium\DI;

use Kdyby\Events\DI\EventsExtension;
use Librette\Solarium\QueryType\DataImport\Query as DataImportQuery;
use Nette;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

class SolariumExtension extends CompilerExtension
{

	const AUTO = null;

	public $defaults = [
		'debugger' => '%debugMode%',
		'endpoints' => [],
		'queryTypes' => [
			DataImportQuery::QUERY_DATAIMPORT => 'Librette\Solarium\QueryType\DataImport\Query',
		],
		'plugins' => [],
	];

	public $endpointDefaults = [
		'host' => '127.0.0.1',
		'port' => 8983,
		'path' => '/solr',
		'core' => null,
		'timeout' => 5,
		'default' => null,
	];


	public function __construct()
	{
		if (PHP_SAPI === 'cli') {
			$this->defaults['debugger'] = false;
		}
	}


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();
		$solarium = $builder->addDefinition($this->prefix('client'))
			->setClass('Librette\Solarium\Client', [[]]);
		if ($config['debugger']) {
			$panel = $builder->addDefinition($this->prefix('panel'))
				->setClass('Librette\Solarium\Diagnostics\Panel')
				->setFactory('Librette\Solarium\Diagnostics\Panel::register', [$this->prefix('@client')]);
			$panel->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
		$this->configureEndpoints($config, $solarium);
		$this->registerQueryTypes($config['queryTypes'], $solarium);
		$this->registerPlugins($config['plugins'], $solarium);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$client = $builder->getDefinition($this->prefix('client'));
		$factory = $client->getFactory();
		$factory->arguments[1] = $builder->getDefinition('events.symfonyProxy');

	}


	/**
	 * @param array
	 * @param ServiceDefinition
	 */
	private function registerPlugins($plugins, ServiceDefinition $solarium)
	{
		foreach ($plugins as $name => $args) {
			if (is_string($args)) {
				$class = $args;
				$options = [];
			} else {
				$class = $args['class'];
				$options = $args['options'];
				$name = isset($args['name']) ? $args['name'] : $name;
			}
			$solarium->addSetup('registerPlugin', [$name, $class, $options]);
		}
	}


	/**
	 * @param array
	 * @param ServiceDefinition
	 */
	private function registerQueryTypes($queryTypes, ServiceDefinition $solarium)
	{
		foreach ($queryTypes as $name => $class) {
			$solarium->addSetup('registerQueryType', [$name, $class]);
		}
	}


	private function configureEndpoints($config, ServiceDefinition $solarium)
	{
		if (empty($config['endpoints'])) {
			$endpoints = ['default' => array_intersect_key($config, $this->endpointDefaults)];
		} else {
			$endpoints = $config['endpoints'];
		}
		$solarium->addSetup('clearEndpoints');
		$default = true;
		foreach ($endpoints as $name => $options) {
			$options += $this->endpointDefaults;
			$options += ['key' => $name];
			$solarium->addSetup('addEndpoint', [array_diff_key($options, ['default' => true])]);
			if (($default === true && $options['default'] === null) || $options['default'] === true) {
				$solarium->addSetup('setDefaultEndpoint', [$name]);
			}
		}
	}


	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->methods['initialize'];
		$initialize->addBody('Librette\Solarium\Diagnostics\Panel::registerBluescreen();');
	}

}
