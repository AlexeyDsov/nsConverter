<?php

namespace AlexeyDsov\NsConverter\Scanners;

class TokenizerListener
{
	/**
	 * @var Tokenizer
	 */
	private $tokenizer;

	/**
	 * @param Tokenizer $tokenizer
	 * @return $this
	 */
	public function setTokenizer($tokenizer)
	{
		$this->tokenizer = $tokenizer;
		return $this;
	}

	public function event()
	{

	}
} 