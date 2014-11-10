<?php

namespace AlexeyDsov\NsConverter\Scanners;

use Evenement\EventEmitter;

class PenjepitCounter implements Scanner
{
	/**
	 * @var callable
	 */
	private $tokenMore;
	/**
	 * @var callable
	 */
	private $tokenLess;
	private $penjepits = 0;

	public function __construct()
	{
		$this->tokenMore = function ($num, $token, EventEmitter $ee) {
			$this->penjepits++;
			$ee->emit('penjepit', [$this->penjepits, $num]);
		};
		$this->tokenLess = function ($num, $token, EventEmitter $ee) {
			$this->penjepits--;
			$ee->emit('penjepit', [$this->penjepits, $num]);
		};
	}

	public function init(EventEmitter $eventEmitter)
	{
		$eventEmitter->on('token:{', $this->tokenMore);
		$eventEmitter->on('token:}', $this->tokenLess);
		$this->penjepits = 0;
	}

	public function stop(EventEmitter $eventEmitter)
	{
		$eventEmitter->removeListener('token:{', $this->tokenMore);
		$eventEmitter->removeListener('token:}', $this->tokenLess);
	}
}