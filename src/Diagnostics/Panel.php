<?php
namespace Librette\Solarium\Diagnostics;

use Kdyby\Events\Subscriber;
use Nette\Object;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Solarium\Client;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostExecute;
use Solarium\Core\Event\PreCreateRequest;
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

	/** @var PostExecute[] */
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
			Events::PRE_CREATE_REQUEST => 'configureSelectQuery',
			Events::POST_EXECUTE       => 'logQuery',
		];
	}


	public function logQuery(PostExecute $request)
	{
		$result = $request->getResult();
		if ($result instanceof SelectResult) {
			$data = $request->getResult()->getData();
			$this->totalTime += $data['responseHeader']['QTime'] - $result->getDebug()->getTiming()->getPhase('process')->getTiming('debug');
			$this->requests[] = $request;

		}
	}


	public function configureSelectQuery(PreCreateRequest $request)
	{
		$query = $request->getQuery();
		if ($query instanceof SelectQuery) {
			$query->getDebug();
			$query->setOptions(['omitheader' => FALSE, 'debug' => TRUE]);
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
		return '<span title="Librette\\Solarium">' . '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3goJDhMWTP1ZHQAAAEJ0RVh0Q29tbWVudABDUkVBVE9SOiBnZC1qcGVnIHYxLjAgKHVzaW5nIElKRyBKUEVHIHY2MiksIHF1YWxpdHkgPSAxMDAKu3x3owAAAt9JREFUKM81jFtIU3EAh3//c9mmbrrlnHa0OcuK6IZlMd2K6kFcUO+FDz5lgRCBFEQUdIEurz1VL0ZBFzOStBIrQohWW9miKC8Zw8627JzcdmzbOed//j1U39vHBx9hjAEALFgGqIYFRZmdqV7ZjFjU1KmwIYTaBggCOA6MA4EAAMwEK6GYBYzhm9eqKj3hxvqno4+z2dxeX72Zzwouj1AngbeBcBwAsBLU2Znx+8h/9VXQYMtq7fOErVKIdO4QPOKTyxf6Txw1PiZATQAcWAkoaT++eZwcNKW6xik0+ubk6cCqprK2TdNDt8tRWCPViBV26EXoOmE0VZpPiaZazP10iLxe1BwOR/LzpH9LUI29nfk041/qr921Jxd9F/+S3HmoVwAKivJdkrwi0QvFxYplDQvTU/5N6+m8PK9lt+xog9Q0+eDOxMTkfM7YmU4LAHW5q2Av4wkllgFYTp8PvC2d1VaH2lDUph/d+6ks1jVKoQ1BeD3EyH3gOI5xhDCdmJpZyovlFfl0xlVTW5iT5akpFy/4GgKQlluzym+DEEYzeqFATZ3nmShSwor6YtbmsKtzKQ5lbl8ADpv1cmTybWw2mW1pDZF49FlfX18mky4rF8+ePtXRsc3Sf1NTtzurwLuO9B4Pb1y30pHj8guMONe3tgv9/bc9nqVDQ6OZH2lCaEEvH7gzLMtyuC20bVeHnFHTmrlmbRCmEX0VrxTcXOvW9onEp2D79jPnzoOznzx1/tbd4UpPfU/vsdHRF5TxBhEHn0f3Hz7+Xv6lO7383YGByO7dgabAyKPHiqK8fhPbv6+r58DB8fFxxpgsy36/XzdKhJArV69Ve73ChUsXU6lUna/WNE23e0lLy+bBwUFKaSKR6O7ujsfj+A8hBACJxWJjY2O/FHV584quri7G2M0b15PJZCgc7uzsfPhwRJIkwzBUVY1EIoQQYlkWY4wDYeT/h1EA7J/xlmX9ff+NfwCdEGcIycBaMwAAAABJRU5ErkJggg==" />'
		. count($this->requests) . ' queries' . ($this->totalTime ? ' / ' . sprintf('%0.1f', $this->totalTime) . 'ms' : '') . '</span>';
	}


	public function getPanel()
	{
		$s = '';
		ob_start();
		echo '<div class="tracy-inner">';
		echo '<table>';
		foreach ($this->requests as $request) {
			/** @var SelectResult $result */
			$result = $request->getResult();
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
			echo '<h3 style="font-weight:bold;font-size: 15px">Parameters:</h3><table style="margin-top: 5px">';
			foreach($data['responseHeader']['params'] as $key => $value) {
				echo '<tr><th>' . $key . '</th><td>' . (is_scalar($value) ? $value : Dumper::dump($value)) . '</td></tr>';
			}
			echo '</table>';

			echo '<h3 style="font-weight: bold;font-size:16px">Timing: </h3>';
			echo '<h4 style="font-weight: bold;font-size:14px">' . ($data['responseHeader']['QTime'] - $result->getDebug()->getTiming()->getPhase('process')->getTiming('debug')) . 'ms (without debug)</h4>';
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
			echo '<h3 style="font-weight: bold;font-size:16px">Explain:</h3>';
			Dumper::dump($result->getDebug()->getExplain(), [Dumper::DEPTH => 6]);
			if(count($result->getDebug()->getExplainOther())) {
				Dumper::dump($result->getDebug()->getExplainOther(), [Dumper::DEPTH => 6]);
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
