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

use AlexeyDsov\NsConverter\Scanners\Simple\DefineConstantScanner;
use AlexeyDsov\NsConverter\Test\CbScanner;
use AlexeyDsov\NsConverter\Test\TestCase;
use Evenement\EventEmitter;

class DefineConstantScannerTest extends TestCase
{
	/**
	 * @group dcst
	 */
	public function testSimple()
	{
		$constantsDetected = [];
		$resultChecker = function (EventEmitter $ee) use (&$constantsDetected) {
			$ee->on('constantDefined', function ($name) use (&$constantsDetected) {
				$constantsDetected[] = $name;
			});
		};

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new DefineConstantScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		$expConstants = ['ABC', 'A2BC5', 'ab_cf'];
		$this->assertEquals($expConstants, $constantsDetected);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'DefineConstantBufferTest.txt');
	}
}