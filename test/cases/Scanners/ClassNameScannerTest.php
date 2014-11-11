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

use AlexeyDsov\NsConverter\Scanners\Simple\ClassNameScanner;
use AlexeyDsov\NsConverter\Test\CbScanner;
use AlexeyDsov\NsConverter\Test\TestCase;
use Evenement\EventEmitter;

class ClassNameScannerTest extends TestCase
{
	/**
	 * @group cnst
	 */
	public function testSimple()
	{
		$classesDetected = [];
		$resultChecker = function (EventEmitter $ee) use (&$classesDetected) {
			$ee->on('classNameDetected', function ($name, $start, $end) use (&$classesDetected) {
				$classesDetected[] = $name.':'.$start.':'.$end;
			});
		};

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new ClassNameScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		$expectationClasses = ['\NsConverter\Form:3:6', '\onPHP\Model:11:16', 'HttpRequest:23:23'];
		$this->assertEquals($expectationClasses, $classesDetected);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'ClassNameBufferTest.txt');
	}
}