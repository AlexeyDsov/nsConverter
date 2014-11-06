<?php

namespace AlexeyDsov\NsConverter\Test;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class CbScanner implements Scanner
{
	/**
	 * @var callable
	 */
	private $cb;

	public function __construct(callable $cb)
	{
		$this->cb = $cb;
	}

	public function init(EventEmitter $eventEmitter)
	{
		call_user_func($this->cb, $eventEmitter);
	}
}