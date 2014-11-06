<?php

namespace AlexeyDsov\NsConverter\Scanners;

use AlexeyDsov\NsConverter\Test\TestCase;
use Evenement\EventEmitter;

class EventEmitterTest extends TestCase {

	/**
	 * @group et
	 */
	public function testEmitter()
	{
		$ee = new EventEmitter();
		$pr = function ($msg) {print $msg.PHP_EOL;};
		$testFunc = function () use ($pr, $ee) {
			$ee->emit('test2');
			$pr("test getted");
		};
		$ee->on('test', $testFunc);

		for ($i = 1; $i < 4; $i++) {
			$pr("test {$i} before");
			$ee->emit('test');
			$pr("test {$i} after");
		}
	}
} 