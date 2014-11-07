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

class NamespaceScannerTest extends TestCase
{
	/**
	 * @group nst
	 */
	public function testSimple()
	{
		$inNamespace = [];
		$outNamespace = [];
		$resultChecker = function (EventEmitter $ee) use (&$inNamespace, &$outNamespace) {
			$ee->on('inNamespace', function ($namespace) use (&$inNamespace) {
				$inNamespace[] = func_get_args();
			});
			$ee->on('outNamespace', function ($namespace) use (&$outNamespace) {
				$outNamespace[] = func_get_args();
			});
		};

		$tokenizer = new Tokenizer();
		$tokenizer->addScanner(new PenjepitCounter());
		$tokenizer->addScanner(new NamespaceScanner());
		$tokenizer->addScanner(new CbScanner($resultChecker));

		$tokenizer->read($this->getTestFile());

		$inNamespaceExp = [
			['converter\testclass', 2, 7],
			['', 45, 47],
			['convert\testclass2', 59, 65],
		];
		$this->assertEquals($inNamespaceExp, $inNamespace);

		$outNamespaceExp = [[45], [53], [80]];
		$this->assertEquals($outNamespaceExp, $outNamespace);
	}

	private function getTestFile()
	{
		return $this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'NamespaceBufferTest.php');
	}

	private function getTestFileContent()
	{
		return file_get_contents(
				$this->getDataPath('buffers' . DIRECTORY_SEPARATOR . 'NamespaceBufferTest.php')
		);
	}
}