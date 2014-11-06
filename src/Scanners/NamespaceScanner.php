<?php

namespace AlexeyDsov\NsConverter\Scanners;

use Evenement\EventEmitter;

class NamespaceScanner implements Scanner
{
	private $buffer = null;
	private $bufferStart = null;
	private $bufferEnd = null;
	private $namespace = '';

	private $penjepitsStartCount = null;
	private $lastPenjepitCount = 0;
	private $lastProcessed = -1;

	public function isBuffer()
	{
		return $this->buffer !== null;
	}

	public function getNamespace()
	{
		return $this->buffer ? '' : $this->namespace;
	}

	public function getBufferStart()
	{
		return $this->bufferStart;
	}

	public function getBufferEnd()
	{
		return $this->bufferEnd;
	}

	public function init(EventEmitter $ee)
	{
		$this->buffer = null;
		$this->bufferStart = null;
		$this->bufferEnd = null;
		$this->namespace = '';

		$this->penjepitsStartCount = 0;
		$this->lastPenjepitCount = 0;
		$this->lastProcessed = -1;

		$newNamespaceFunc = function ($num, $subject, EventEmitter $ee) {
			$this->penjepitsStartCount = null;
			$this->bufferStart = $this->buffer = $num;
			$this->namespace = '';
			$this->lastProcessed = $num;
		};
		$nsSeparatorOrString = function ($num, $subject, EventEmitter $ee) {
			if ($this->buffer !== null) {
				$this->lastProcessed = $num;
				$this->namespace .= $subject[1];
			}
		};
		$nsBufferEnd = function ($num, $subject, EventEmitter $ee) {
			if ($this->buffer !== null) {
				$this->lastProcessed = $num;
				$this->bufferEnd = $num;
				$this->buffer = null;
//				$this->penjepitsStartCount = $this->lastPenjepitCount;
				if ($subject == '{') {
					$this->penjepitsStartCount = $this->lastPenjepitCount;
	//				$this->penjepitCounter = (new PenjepitCounter())->init();
	//				$this->penjepitCounter->process($subject, $i);
				}

				$ee->emit('inNamespace', [$this->namespace, $this->bufferStart, $this->bufferEnd]);
			}
		};
		$nsEnd = function ($num, $subject, EventEmitter $ee) {
			if ($this->lastProcessed != $num) {
				if ($this->penjepitsStartCount !== null && $this->penjepitsStartCount < $this->lastPenjepitCount) {
					$this->namespace = '';
					$this->penjepitCounter = null;
				}
			}
		};
		$penjepitListener = function ($penjepits, $num) {
			$this->lastPenjepitCount = $penjepits;
		};

		$ee->on('token:'.T_NAMESPACE, $newNamespaceFunc);
		$ee->on('token:'.T_STRING, $nsSeparatorOrString);
		$ee->on('token:'.T_NS_SEPARATOR, $nsSeparatorOrString);
		$ee->on('token:;', $nsBufferEnd);
		$ee->on('token:{', $nsBufferEnd);
		$ee->on('token', $nsEnd);
		$ee->on('penjepit', $penjepitListener);
	}

	private function process($i, $subject, EventEmitter $ee)
	{
//		if ($this->penjepitCounter) {
//			$this->penjepitCounter->process($subject, $i);
//		}
		if (is_array($subject) && $subject[0] == T_NAMESPACE) {
//			$this->penjepitCounter = null;
			$this->penjepitsStartCount = null;
//			$this->penjepitsStartCount = $this->lastPenjepitCount;
			$this->buffer = $i;
			$this->bufferStart = $i;
			$this->namespace = '';
		} elseif (!is_null($this->buffer)) {
			if (is_array($subject) && in_array($subject[0], [T_STRING, T_NS_SEPARATOR])) {
				$this->namespace .= $subject[1];
			} elseif (is_string($subject) && in_array($subject, [';', '{'])) {
				$this->bufferEnd = $i;
				$this->buffer = null;
				$this->penjepitsStartCount = $this->lastPenjepitCount;
//				if ($subject == '{') {
//					$this->penjepitCounter = (new PenjepitCounter())->init();
//					$this->penjepitCounter->process($subject, $i);
//				}
			}
		} elseif ($this->penjepitsStartCount !== null && !$this->penjepitCounter->isBuffer()) {
			$this->namespace = '';
			$this->penjepitCounter = null;
		}
	}

	private function onPenjepit($count, $tokenNum)
	{
		$this->lastPenjepitCount = $count;
	}
}