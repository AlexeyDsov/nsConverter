<?php

namespace AlexeyDsov\NsConverter\Scanners;

use Evenement\EventEmitter;

class FunctionScanner implements Scanner
{
	private $buffer = false;
	private $inFunction = false;
	private $functionName = null;

	private $penjepitCountStart = null;

	private $lastPenjepitCount = null;

	/**
	 * @deprecated
	 * @return bool
	 */
	public function isBuffer()
	{
		return $this->buffer !== null;
	}

	/**
	 * @return string
	 */
	public function getFunctionName()
	{
		if (!$this->buffer && $this->functionName)
			return $this->functionName;
	}

	public function init(EventEmitter $ee)
	{
		$this->buffer = false;
		$this->inFunction = false;
		$this->functionName = null;

		$this->penjepitCountStart = null;
		$this->penjepitCountLast = null;

		$startFunc = function ($num, $subject, EventEmitter $ee) {
			$this->buffer = true;
			$this->functionName = '';
			$this->inFunction = true;
			$this->penjepitCountStart = null;
//			$this->penjepitCountStart = $this->lastPenjepitCount;
//			$this->penjepitCounter = null;
		};
		$funcName = function ($num, $subject, EventEmitter $ee) {
			if ($this->buffer) {
				$this->functionName .= $subject[1];
			}
		};
		$funcNameEnd = function ($num, $subject, EventEmitter $ee) {
			if ($this->buffer) {
				$this->buffer = false;
			}
		};
		$inFunctionBody = function ($num, $subject, EventEmitter $ee) {
			if ($this->inFunction && $this->penjepitCountStart === null) {
				$this->penjepitCountStart = $this->lastPenjepitCount;
				$ee->emit('functionStart', [$this->functionName, $num]);
			}

		};

		$penjepitListener = function ($penjepits, $num) use ($ee) {
			$this->lastPenjepitCount = $penjepits;
			if ($this->inFunction && $this->penjepitCountStart !== null && $this->lastPenjepitCount < $this->penjepitCountStart) {
				$ee->emit('functionEnd', [$this->functionName, $num]);
				$this->inFunction = false;
				$this->buffer = false;
				$this->functionName = null;
				$this->penjepitCountStart = null;
//				$this->penjepitCounter = null;
			}
		};

		$ee->on('token:'.T_FUNCTION, $startFunc);
		$ee->on('token:'.T_STRING, $funcName);
		$ee->on('token:(', $funcNameEnd);
		$ee->on('token:{', $inFunctionBody);
		$ee->on('penjepit', $penjepitListener);
	}
}