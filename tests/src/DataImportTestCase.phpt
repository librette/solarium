<?php
namespace LibretteTests\Solarium;

use Librette\Solarium\QueryType\DataImport\Query;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @author David Matějka
 */
class DataImportTestCaseTest extends AbstractSolariumTestCase
{

	public function setUp()
	{
		parent::setUp();
		$this->client->registerQueryType(Query::QUERY_DATAIMPORT, 'Librette\Solarium\QueryType\DataImport\Query');
	}


	public function testCreateRequest()
	{
		$query = $this->client->createQuery(Query::QUERY_DATAIMPORT);
		Assert::type('Librette\Solarium\QueryType\DataImport\Query', $query);
		/** @var $query Query */
		$query->setClean(TRUE);
		$query->setCommit(TRUE);
		$query->setOptimize(TRUE);
		$query->setCommand(Query::COMMAND_DELTA_IMPORT);
		$query->setEntity('test');
		$request = $this->client->createRequest($query);
		Assert::same('Solarium\Core\Client\Request', get_class($request));
		Assert::same('wt=json&json.nl=flat&command=delta-import&clean=true&commit=true&optimize=true&debug=false&verbose=false&entity=test', $request->getQueryString());

	}


	public function testCustomParameters()
	{
		/** @var $query Query */
		$query = $this->client->createQuery(Query::QUERY_DATAIMPORT);
		$query->setCustomParameters([
			'foo'   => 'bar',
			'lorem' => 'ipsum',
		]);
		$query->setCustomParameter('dolor', 'sit');
		$request = $this->client->createRequest($query);
		Assert::match('%a%&foo=bar&lorem=ipsum&dolor=sit', $request->getQueryString());

	}

}


\run(new DataImportTestCaseTest());
