<?php
namespace Librette\Solarium\Diagnostics;

use Kdyby\Events\Subscriber;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Solarium\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateRequest;
use Solarium\Core\Event\PostExecute;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecute;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Query\QueryInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\HttpException;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Debug\TimingPhase;
use Solarium\QueryType\Select\Result\Result as SelectResult;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\IBarPanel;


/**
 * @author David Matejka
 */
class Panel extends Object implements IBarPanel, Subscriber
{

	/** @var int logged time */
	private $totalTime = NULL;

	/** @var array of [query => Query, request => RequestInterface, result => ResultInterface] */
	private $requests = [];

	/** @var Client */
	private $solarium;


	/**
	 * @param Client
	 */
	public function __construct(Client $solarium)
	{
		$this->solarium = $solarium;
	}


	public function getSubscribedEvents()
	{
		return [
			Events::PRE_EXECUTE          => 'logQuery',
			Events::POST_CREATE_REQUEST  => 'logRequest',
			Events::PRE_EXECUTE_REQUEST  => 'logPreExecute',
			Events::POST_EXECUTE_REQUEST => 'logPostExecute',
			Events::POST_EXECUTE         => 'logResult',
		];
	}


	public function logQuery(PreExecute $event)
	{
		$this->requests[spl_object_hash($event->getQuery())] = ['query' => $event->getQuery(), 'start' => microtime(TRUE)];
		$query = $event->getQuery();
		if ($query instanceof SelectQuery) {
			$query->getDebug();
			$query->setOptions(['omitheader' => FALSE, 'debug' => TRUE]);
		}

	}


	public function logRequest(PostCreateRequest $event)
	{
		$this->requests[spl_object_hash($event->getQuery())] += ['request' => $event->getRequest()];
	}


	public function logResult(PostExecute $event)
	{
		$this->requests[spl_object_hash($event->getQuery())] += ['result' => $event->getResult(), 'end' => microtime(TRUE)];
		$result = $event->getResult();
		$data = $event->getResult()->getData();
		$this->totalTime += isset($data['responseHeader']['QTime']) ? $data['responseHeader']['QTime'] : 0;
		if ($result instanceof SelectResult && $result->getDebug()->getTiming() !== NULL) {
			$this->totalTime -= $result->getDebug()->getTiming()->getPhase('process')->getTiming('debug');
		}
	}


	public function logPreExecute(PreExecuteRequest $event)
	{
		foreach ($this->requests as &$request) {
			if ($request['request'] === $event->getRequest()) {
				$request['requestStart'] = microtime(TRUE);
				break;
			}
		}
	}


	public function logPostExecute(PostExecuteRequest $event)
	{
		foreach ($this->requests as &$request) {
			if ($request['request'] === $event->getRequest()) {
				$request['requestEnd'] = microtime(TRUE);
				break;
			}
		}
	}


	public static function renderException($e)
	{
		if (!$e instanceof HttpException) {
			return;
		}
		$data = $e->getBody();
		try {
			$data = Json::decode($data);
			if (isset($data->error->trace)) {
				$data->error->trace = explode("\n", $data->error->trace);
			}
		} catch (JsonException $e) {
		}
		$message = NULL;
		if (is_string($data)) {
			$dump = '<pre>' . htmlspecialchars($data) . "</pre>";
		} else {
			$dump = Dumper::toHtml($data);
			$message = isset($data->error->msg) ? $data->error->msg : NULL;
		}

		return [
			'tab'   => 'SOLR',
			'panel' => ($message ? "<h2>$message</h2>" : '') .
				'<h3>Code: ' . $e->getCode() . '</h3><br>' .
				$dump,
		];
	}


	public function getTab()
	{
		$requests = array_filter($this->requests, function ($value) {
			return isset($value['result']);
		});
		if (!count($requests)) {
			return '';
		}
		$altTotalTime =  array_reduce($requests, function ($sum, $value) {
			return $sum + ($value['end'] - $value['start']) * 1000;
		}, 0);

		$alt2TotalTime = array_reduce($requests, function ($sum, $value) {
			return $sum + ($value['requestEnd'] - $value['requestStart']) * 1000;
		}, 0);

		return '<span title="Librette\\Solarium">' . '<svg viewBox="0 0 558 558"><path fill="#da4327" d="m265.62558,537.51709c-19.333496,-10.834167 -35.063995,-19.79303 -34.956604,-19.908508c0.472107,-0.507813 155.608398,18.27533 155.608398,18.840332c0.000183,1.629822 -30.023926,11.543823 -46.499878,15.354309c-9.323608,2.156311 -31.119629,5.504517 -35.5,5.453247c-2.648438,-0.030945 -12.053101,-4.833923 -38.651917,-19.73938zm69.401917,-46.508575c-52.662537,-10.510498 -95.745911,-19.353027 -95.740997,-19.649963c0.009979,-0.576477 282.240173,-56.862427 282.856079,-56.410706c0.807251,0.591858 -11.934479,20.315613 -20.808807,32.211609c-17.588745,23.577698 -60.570496,63.36853 -68.115967,63.059143c-1.342224,-0.054993 -45.527832,-8.699585 -98.190308,-19.210083zm-103.569305,-66.193665c0.37439,-0.380005 73.274384,-41.320587 161.999969,-90.979126c152.381927,-85.286072 161.371429,-90.169891 162.260101,-88.152649c1.512756,3.433868 1.281311,59.652649 -0.288696,70.135559c-3.411011,22.774261 -9.042236,44.639526 -16.261719,63.141479l-3.957153,10.141449l-4.216553,0.610992c-2.319092,0.33606 -68.791656,8.245483 -147.716644,17.576538c-78.924988,9.331116 -145.524994,17.24707 -148,17.591125c-2.475006,0.344055 -4.193695,0.314636 -3.819305,-0.06543l0,0.000061zm114.076324,-188.944916l136.242981,-147.448708l5.867981,6.1987c17.366211,18.344788 36.060944,47.911064 47.896393,75.749397c8.131348,19.126129 18.043213,53.063904 16.064453,55.004486c-0.827148,0.811188 -340.896637,157.944824 -341.825134,157.944824c-0.269318,0 60.819672,-66.351929 135.753265,-147.4487l0.000061,0zm-168.794434,113.415131c0.321899,-0.843445 36.079407,-78.468445 79.461121,-172.500015c75.930664,-164.582512 78.970154,-170.956108 81.400452,-170.690407c4.54895,0.497299 27.244751,7.03109 37.315735,10.742661c13.640076,5.026886 39.846985,18.006454 51.953979,25.731415c16.607483,10.596451 43.906128,32.305008 43.906128,34.915253c0,0.411575 -64.799988,60.664207 -144,133.894707c-79.200012,73.230545 -145.490112,134.562454 -147.31131,136.293167c-1.82132,1.730743 -3.048004,2.456696 -2.726105,1.61322zm-155.851379,-57.680176l-20.888702,-37.474838l0.615173,-7.278671c0.865112,-10.23349 5.183716,-31.710083 9.14151,-45.460358c3.367096,-11.698303 12.77771,-36.900116 13.212616,-35.383667c0.494598,1.72464 19.354401,162.596466 19.089081,162.827606c-0.15448,0.134613 -9.680786,-16.618927 -21.169678,-37.230072zm113.711487,34.713745c0.323486,-1.924988 8.494995,-70.550003 18.159088,-152.5c9.664001,-81.949989 17.769104,-149.918312 18.011108,-151.040718c0.709595,-3.289902 23.792206,-11.293549 48.507111,-16.819336c19.571686,-4.375862 35.405579,-5.974937 59,-5.958443c18.176392,0.012703 37.34729,1.332451 38.677368,2.662582c0.334534,0.334518 -181.288483,325.548981 -182.427979,326.655914c-0.283081,0.275024 -0.250092,-1.074982 0.072998,-3l0.000305,0zm-46.608093,-6c-0.324005,-0.824982 -9.432404,-46.19455 -20.240906,-100.821259l-19.651917,-99.321281l7.220703,-9.481262c19.02301,-24.97876 40.144226,-45.070404 65.45752,-62.266815c9.730377,-6.610306 23.588501,-15.109375 24.636383,-15.109375c0.286102,0 -10.426178,55.012497 -23.805084,122.249992c-13.378815,67.237518 -26.283203,132.149994 -28.676422,144.25c-2.393188,12.100006 -4.616394,21.325012 -4.940277,20.5z"/></svg>'
		. '<span class="tracy-label">' . count($requests) . ' queries' . ($this->totalTime ? ' / ' . sprintf('%0.1f', $this->totalTime) . 'ms' : '') . ' / ' . sprintf('%0.1f', $alt2TotalTime) . 'ms</span></span>';
	}


	public function getPanel()
	{
		$s = '';
		ob_start();
		echo '<div class="tracy-inner">';
		echo '<table>';
		foreach (array_filter($this->requests, function ($value) {
			return isset($value['result']);
		}) as $req) {
			/** @var QueryInterface $query */
			$query = $req['query'];
			/** @var Request $request */
			$request = $req['request'];
			/** @var ResultInterface $result */
			$result = $req['result'];

			$data = $result->getData();

			echo '<tr><td>';
			echo '<h3 style="font-weight:bold;font-size: 15px">Rows:</h3>';
			if (isset($data['grouped'])) {
				$groupInfo = [];
				foreach ($data['grouped'] as $name => $info) {
					$groupInfo[] = $name . ' - ' . $info['matches'] . (isset($info['ngroups']) ? ' (' . $info['ngroups'] . ' groups)' : '');
				}
				echo implode(', ', $groupInfo);
			} else {
				echo $data['response']['numFound'];
			}
			echo '<h3 style="font-weight:bold;font-size: 15px">Request:</h3>';
			echo '<div>' . $request->getMethod() . ': ' . $request->getUri() . '</div>';
			echo '<h3 style="font-weight:bold;font-size: 13px">Parameters:</h3><table style="margin-top: 5px">';
			foreach ($request->getParams() as $key => $value) {
				echo '<tr><th>' . $key . '</th><td>';
				Dumper::dump($value);
				echo '</td></tr>';
			}
			echo '</table>';

			echo '<h3 style="font-weight: bold;font-size:16px">Timing: </h3>';
			$hasTiming = $result instanceof SelectResult && $result->getDebug()->getTiming();
			if ($hasTiming) {
				$debugTime = $result->getDebug()->getTiming()->getPhase('process')->getTiming('debug');
			} else {
				$debugTime = 0;
			}
			echo '<h4 style="font-weight: bold;font-size:14px">' . ($data['responseHeader']['QTime'] - $debugTime) . 'ms (without debug)</h4>';
			if ($hasTiming) {
				echo '<table>';
				/** @var TimingPhase $phase */
				foreach ($result->getDebug()->getTiming() as $name => $phase) {
					echo '<tr>';
					echo '<th>' . $name . "</th>";
					echo '<td>' . $phase->getTime() . 'ms</td>';
					echo '<td><table>';
					foreach ($phase->getTimings() as $key => $time) {
						echo '<tr><th>' . $key . "</t><td>" . $time . 'ms</td></tr>';
					}
					echo '</table></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			if ($result instanceof SelectQuery) {
				echo '<h3 style="font-weight: bold;font-size:16px">Explain:</h3>';
				Dumper::dump($result->getDebug()->getExplain(), [Dumper::DEPTH => 6]);
				if (count($result->getDebug()->getExplainOther())) {
					Dumper::dump($result->getDebug()->getExplainOther(), [Dumper::DEPTH => 6]);
				}
			}

			echo '</td></tr>';
		}
		echo '</table>';
		echo '</div>';
		$s .= ob_get_clean();

		return empty($this->requests) ? '' :
			'<h1>Queries: ' . count($this->requests) . ($this->totalTime !== NULL ? ', time: ' . $this->totalTime . ' ms' : '') . '</h1>' . $s;
	}


	public static function registerBluescreen()
	{
		Debugger::getBlueScreen()->addPanel(['Librette\Solarium\Diagnostics\Panel', 'renderException']);
	}


	public static function register(Client $solarium)
	{
		$panel = new self($solarium);
		Debugger::getBar()->addPanel($panel);

		return $panel;
	}

}
