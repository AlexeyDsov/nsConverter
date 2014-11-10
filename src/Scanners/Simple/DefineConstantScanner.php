<?php

namespace AlexeyDsov\NsConverter\Scanners\Simple;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class DefineConstantScanner implements Scanner
{
	public function init(EventEmitter $eventEmitter)
	{
		$buffer = false;
		$lastNum = null;
		$bufferDetector = function ($num, $subject, EventEmitter $ee) use (&$lastNum, &$buffer) {
			if ($subject[1] == 'define') {
				$buffer = true;
				$lastNum = $num;
			}
		};
		$okSkip = function ($num, $subject, EventEmitter $ee) use (&$lastNum, &$buffer) {
			if ($buffer) {
				$lastNum = $num;
			}
		};
		$nameCreator = function ($num, $subject, EventEmitter $ee) use (&$lastNum, &$buffer) {
			if ($buffer && preg_match('~^[\'"]([\w]+)[\'"]$~iu', $subject[1], $match)) {
				$ee->emit('constantDefined', [$match[1]]);
				$buffer = false;
				$lastNum = null;
			}
		};
		$notConstant = function ($num, $subject, EventEmitter $ee) use (&$lastNum, &$buffer) {
			if ($buffer && $lastNum !== null && $lastNum != $num) {
				$lastNum = null;
				$buffer = false;
			}
		};

		$eventEmitter->on('token:'.T_STRING, $bufferDetector);
		$eventEmitter->on('token:(', $okSkip);
		$eventEmitter->on('token:'.T_WHITESPACE, $okSkip);
		$eventEmitter->on('token:'.T_CONSTANT_ENCAPSED_STRING, $nameCreator);
		$eventEmitter->on('token', $notConstant);
	}
}
