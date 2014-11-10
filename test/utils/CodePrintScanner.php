<?php

namespace AlexeyDsov\NsConverter\Test;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class CodePrintScanner implements Scanner
{
	public function init(EventEmitter $eventEmitter)
	{
		$eventEmitter->on('token', function ($num, $subject) {
			print $num.':'.(is_string($subject) ? $subject : $subject[1]).PHP_EOL;
		});
	}
}