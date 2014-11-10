<?php

namespace AlexeyDsov\NsConverter\Scanners\Simple;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class ClassScanner implements Scanner
{
	public function init(EventEmitter $eventEmitter)
	{
		$currentPenjepit = 0;

		$classDetector = function ($num, $subject, EventEmitter $ee) use (&$currentPenjepit) {
			$this->registerClassStart($ee, $currentPenjepit, $num);
		};
		$penjepitCounter = function ($count, $num) use (&$currentPenjepit) {
			$currentPenjepit = $count;
		};

		$eventEmitter->on('token:'.T_CLASS, $classDetector);
		$eventEmitter->on('token:'.T_INTERFACE, $classDetector);
		$eventEmitter->on('token:'.T_TRAIT, $classDetector);
		$eventEmitter->on('penjepit', $penjepitCounter);
	}

	private function registerClassStart(EventEmitter $eventEmitter, $penjepitStart, $classStart)
	{
		$className = null;
		$currentPenjepit = null;
		$buffer = true;

		$inBuffer = function ($num, $subject, EventEmitter $ee) use (&$className, &$buffer) {
			if ($buffer) {
				$className .= $subject[1];
			}
		};
		$bufferEnd = function () use ($inBuffer, &$bufferEnd, $eventEmitter, &$className, &$classStart, &$buffer) {
			if ($buffer) {
				$buffer = false;
				if ($className) {
					$eventEmitter->emit('classStarted', [$className, $classStart]);
				}
				$eventEmitter->removeListener('token:' . T_STRING, $inBuffer);
				$eventEmitter->removeListener('token:' . T_EXTENDS, $bufferEnd);
				$eventEmitter->removeListener('token:' . T_IMPLEMENTS, $bufferEnd);
				$eventEmitter->removeListener('token:{', $bufferEnd);
				$eventEmitter->removeListener('token:(', $bufferEnd);
				$eventEmitter->removeListener('token:;', $bufferEnd);
				$eventEmitter->removeListener('penjepit', $bufferEnd);
			}
		};
		$penjepiter = function ($count, $num) use ($inBuffer, $bufferEnd, &$penjepiter, $eventEmitter, &$penjepitStart, &$currentPenjepit) {
			$currentPenjepit = $count;
			if (!is_null($penjepitStart) && $penjepitStart <= $currentPenjepit) {
				$eventEmitter->removeListener('token:'.T_STRING, $inBuffer);
				$eventEmitter->removeListener('token:'.T_EXTENDS, $bufferEnd);
				$eventEmitter->removeListener('token:'.T_IMPLEMENTS, $bufferEnd);
				$eventEmitter->removeListener('token:{', $bufferEnd);
				$eventEmitter->removeListener('token:(', $bufferEnd);
				$eventEmitter->removeListener('token:;', $bufferEnd);
				$eventEmitter->removeListener('penjepit', $bufferEnd);
				$eventEmitter->removeListener('penjepit', $penjepiter);
				$currentPenjepit = null;
			}
		};

		$eventEmitter->on('token:'.T_STRING, $inBuffer);
		$eventEmitter->on('token:'.T_EXTENDS, $bufferEnd);
		$eventEmitter->on('token:'.T_IMPLEMENTS, $bufferEnd);
		$eventEmitter->on('token:{', $bufferEnd);
		$eventEmitter->on('token:(', $bufferEnd);
		$eventEmitter->on('token:;', $bufferEnd);
		$eventEmitter->on('penjepit', $bufferEnd);
		$eventEmitter->on('penjepit', $penjepiter);
	}
}