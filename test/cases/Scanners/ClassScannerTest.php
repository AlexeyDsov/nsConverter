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

use AlexeyDsov\NsConverter\Business\NsClass;
use AlexeyDsov\NsConverter\Scanners\Complex\ClassStorageScanner;
use AlexeyDsov\NsConverter\Scanners\Simple\ClassScanner;
use AlexeyDsov\NsConverter\Test\CbScanner;
use AlexeyDsov\NsConverter\Test\TestCase;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use Evenement\EventEmitter;

class ClassScannerTest extends TestCase
{
	public function testSimple()
	{
		$classesDetected = [];
		$resultChecker = function (EventEmitter $ee) use (&$classesDetected) {
			$ee->on('classStarted', function ($name, $num) use (&$classesDetected) {
				$classesDetected[] = $name.':'.$num;
			});
		};

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new PenjepitCounter());
		$tokenizer->addScanner(new ClassScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		$expConstants = ['TestSecondClassToParse:6', 'A:24', 'TestOneClassToParse:91'];
		$this->assertEquals($expConstants, $classesDetected);
	}

	/**
	 * @group cst
	 */
	public function testClassStorageScanner()
	{
		$classStorage = new ClassStorage();

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new PenjepitCounter());
		$tokenizer->addScanner(new NamespaceScanner());
		$tokenizer->addScanner(new ClassScanner());
		$tokenizer->addScanner((new ClassStorageScanner())->setClassStorage($classStorage)->setNewNamespace('\AlexeyDsov\NsConverter'));

		$tokenizer->read($this->getTestFile());

		$classes = array_map(
			function (NsClass $class) {
				return $class->getFullName().':'.$class->getFullNewName();
			},
			$classStorage->getListClasses()
		);
		$expectation = [
			'\TestSecondClassToParse:\AlexeyDsov\NsConverter\TestSecondClassToParse',
			'\convert\testclass2\A:\AlexeyDsov\NsConverter\A',
			'\converter\testclass\TestOneClassToParse:\AlexeyDsov\NsConverter\TestOneClassToParse',
		];
		$this->assertEquals($expectation, $classes);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'ClassBufferTest.php');
	}
}