<?php

namespace AlexeyDsov\NsConverter\Scanners\Simple;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class ClassScanner implements Scanner
{
	public function init(EventEmitter $eventEmitter)
	{
		$buffer = false;
		$inClass = false;
		$className = null;
		$penjepitStart = null;
		$currentPenjepit = null;
		$classStart = null;

		$classDetector = function ($num, $subject, EventEmitter $ee) use (&$buffer, &$inClass, &$className, &$penjepitStart, &$currentPenjepit, &$classStart) {
			$buffer = true;
			$className = '';
			$inClass = true;
			$penjepitStart = $currentPenjepit;
			$classStart = $num;
		};
		$inBuffer = function ($num, $subject, EventEmitter $ee) use (&$buffer, &$inClass, &$className, &$penjepitStart, &$currentPenjepit) {
			if ($buffer) {
				$className .= $subject[1];
			}
		};
		$bufferEnd = function () use ($eventEmitter, &$buffer, &$inClass, &$className, &$penjepitStart, &$currentPenjepit, &$classStart) {
			if ($buffer) {
				$buffer = false;
				if ($className) {
					$eventEmitter->emit('classStarted', [$className, $classStart]);
				}
			}
		};

		$penjepiter = function ($count, $num) use (&$buffer, &$inClass, &$className, &$penjepitStart, &$currentPenjepit, &$classStart) {
			$currentPenjepit = $count;
			if (!is_null($penjepitStart) && $penjepitStart <= $currentPenjepit) {
				$inClass = false;
				$buffer = false;
				$className = '';
				$currentPenjepit = null;
				$classStart = null;
			}
		};

		$eventEmitter->on('token:'.T_CLASS, $classDetector);
		$eventEmitter->on('token:'.T_INTERFACE, $classDetector);
		$eventEmitter->on('token:'.T_TRAIT, $classDetector);
		$eventEmitter->on('token:'.T_STRING, $inBuffer);
		$eventEmitter->on('token:'.T_EXTENDS, $bufferEnd);
		$eventEmitter->on('token:'.T_IMPLEMENTS, $bufferEnd);
		$eventEmitter->on('token:{', $bufferEnd);
		$eventEmitter->on('token:(', $bufferEnd);
		$eventEmitter->on('token:;', $bufferEnd);
		$eventEmitter->on('penjepit', $bufferEnd);
		$eventEmitter->on('penjepit', $penjepiter);
	}

//	public function process($subject, $i)
//	{
//		if ($this->penjepitCounter) {
//			$this->penjepitCounter->process($subject, $i);
//		}
//
//		if (is_array($subject) && in_array($subject[0], [T_CLASS, T_INTERFACE, T_TRAIT])) {
//			$this->buffer = true;
//			$this->className = '';
//			$this->inClass = true;
//			$this->penjepitCounter = null;
//		} elseif ($this->buffer) {
//			$isBufferEnd1 = is_array($subject) && in_array($subject[0], [T_EXTENDS, T_IMPLEMENTS]);
//			$isBufferEnd2 = is_string($subject) && in_array($subject, ['{', ';', '(']);
//
//			if (is_array($subject) && in_array($subject[0], [T_STRING])) {
//				$this->className .= $subject[1];
//			} elseif ($isBufferEnd1 || $isBufferEnd2) {
//				$this->buffer = false;
//			}
//		}
//		if ($this->inClass && !$this->penjepitCounter && is_string($subject) && $subject == '{') {
//			$this->penjepitCounter = (new PenjepitCounter())->init();
//			$this->penjepitCounter->process($subject, $i);
//		}
//		if ($this->penjepitCounter && !$this->penjepitCounter->isBuffer()) {
//			$this->inClass = false;
//			$this->buffer = false;
//			$this->className = null;
//			$this->penjepitCounter = null;
//		}
//	}
}