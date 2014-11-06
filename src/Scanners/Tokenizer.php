<?php

namespace AlexeyDsov\NsConverter\Scanners;

use Evenement\EventEmitter;

class Tokenizer
{
	/**
	 * @var Scanner[]
	 */
	private $scanners = [];

	/**
	 * @param Scanner $scanner
	 * @return $this
	 */
	public function addScanner(Scanner $scanner)
	{
		$this->scanners[] = $scanner;
		return $this;
	}

	public function read($path)
	{
		$ee = new EventEmitter();
		foreach ($this->scanners as $scanner) {
			$scanner->init($ee);
		}

		foreach (token_get_all(file_get_contents($path)) as $num => $token) {
			if (is_array($token)) {
				$ee->emit('token:'.$token[0], [$num, $token, $ee]);
			} else {
				$ee->emit('token:'.$token, [$num, $token, $ee]);
			}
			$ee->emit('token', [$num, $token, $ee]);
		}
		$ee->emit('end');
		$ee->removeAllListeners();
	}
}