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

use AlexeyDsov\NsConverter\Scanners\Simple\ClassScanner;
use AlexeyDsov\NsConverter\Test\CbScanner;
use AlexeyDsov\NsConverter\Test\CodePrintScanner;
use AlexeyDsov\NsConverter\Test\TestCase;
use Evenement\EventEmitter;

class ClassScannerTest extends TestCase
{
	/**
	 * @group cst
	 */
	public function testSimple()
	{
		$classesDetected = [];
		$resultChecker = function (EventEmitter $ee) use (&$classesDetected) {
			$ee->on('classStarted', function ($name, $num) use (&$classesDetected) {
				$classesDetected[] = $name.':'.$num;
			});
		};

		$tokenizer = new Tokenizer();
//		$tokenizer->addScanner(new CodePrintScanner());
		$tokenizer->addScanner(new PenjepitCounter());
		$tokenizer->addScanner(new ClassScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		print_r($classesDetected);
		$expConstants = ['TestSecondClassToParse:6', 'A:24', 'TestOneClassToParse:91'];
//		$this->assertEquals($expConstants, $classesDetected);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'ClassBufferTest.php');
	}
}