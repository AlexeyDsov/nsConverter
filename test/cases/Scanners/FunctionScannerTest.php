<?php

/* * *************************************************************************
 *   Copyright (C) 2012 by Alexey Denisov                                  *
 *   alexeydsov@gmail.com                                                  *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 * ************************************************************************* */

namespace AlexeyDsov\NsConverter\Scanners;

use AlexeyDsov\NsConverter\Test\CbScanner;
use AlexeyDsov\NsConverter\Test\TestCase;
use Evenement\EventEmitter;

class FunctionScannerTest extends TestCase
{
	/**
	 * @group fst
	 */
	public function testSimple()
	{
		$functionStart = [];
		$functionEnd = [];
		$resultChecker = function (EventEmitter $ee) use (&$functionStart, &$functionEnd) {
			$ee->on('functionStart', function () use (&$functionStart) {
				$functionStart[] = func_get_args();
			});
			$ee->on('functionEnd', function () use (&$functionEnd) {
				$functionEnd[] = func_get_args();
			});
		};

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new PenjepitCounter());
		$tokenizer->addScanner(new FunctionScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		$expStarts = [
			['doSomething', 23],
			['', 90]
		];
		$this->assertEquals($expStarts, $functionStart);
		$expEnds = [
			['doSomething', 51],
			['', 101]
		];
		$this->assertEquals($expEnds, $functionEnd);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'FunctionBufferTest.php');
	}
}