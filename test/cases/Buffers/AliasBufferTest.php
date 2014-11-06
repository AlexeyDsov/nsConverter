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

namespace Onphp\NsConverter\Buffers;

use AlexeyDsov\NsConverter\Buffers\Complex\AliasBuffer;
use AlexeyDsov\NsConverter\Buffers\ChainBuffer;
use AlexeyDsov\NsConverter\Buffers\Simple\ClassBuffer;
use AlexeyDsov\NsConverter\Buffers\Simple\NamespaceBuffer;
use AlexeyDsov\NsConverter\Test\TestCase;

class AliasBufferTest extends TestCase
{
	public function testSimple()
	{
		$aliasBuffer = $this->execute();
		
		$this->assertEquals(['Class1' => '\SomeNs\Sub\Class1'], $aliasBuffer->getAliases());
		$this->assertEquals([[6, 18]], $aliasBuffer->getBuffers());
	}

	public function testMultiple()
	{
		$aliasExp = [
			'Class1' => '\SomeNs\Sub\Class1',
			'\SomeNs2\Sub3' => '\SomeNs\Sub2',
			'Exception' => '\Exception',
		];
		
		$aliasBuffer = $this->execute('2');
		
		$this->assertEquals($aliasExp, $aliasBuffer->getAliases());
		$this->assertEquals([[6, 35]], $aliasBuffer->getBuffers());
	}

	public function testMixed()
	{
		$aliasExp = [
			'Class1' => '\SomeNs\Sub\Class1',
			'\SomeNs2\Sub3' => '\SomeNs\Sub2',
			'Exception' => '\Exception',
			'Exception2' => '\Exception2',
			'Class2' => '\SomeNs\Sub\Class1',
		];
		$buffersExp = [
			[6, 35],
			[37, 41],
			[43, 55],
		];
		
		$aliasBuffer = $this->execute('3');
		
		$this->assertEquals($aliasExp, $aliasBuffer->getAliases());
		$this->assertEquals($buffersExp, $aliasBuffer->getBuffers());
	}
	
	/**
	 * @param string $fileNum
	 * @return \AlexeyDsov\NsConverter\Buffers\Complex\AliasBuffer
	 */
	private function execute($fileNum = '')
	{
		$chain = new ChainBuffer();
		$chain->addBuffer($nsBuffer = new \AlexeyDsov\NsConverter\Buffers\Simple\NamespaceBuffer());
		$chain->addBuffer($classBuffer = new \AlexeyDsov\NsConverter\Buffers\Simple\ClassBuffer());
		$chain->addBuffer($aliasBuffer = $this->getService($nsBuffer, $classBuffer));
		
		$file = $this->getTestFileContent($fileNum);
		$subjects = token_get_all($file);

		$chain->init();
		foreach ($subjects as $i => $subject) {
			$chain->process($subject, $i);
		}
		
		return $aliasBuffer;
	}

	/**
	 * @return \AlexeyDsov\NsConverter\Buffers\Complex\AliasBuffer
	 */
	private function getService(\AlexeyDsov\NsConverter\Buffers\Simple\NamespaceBuffer $nsBuffer, \AlexeyDsov\NsConverter\Buffers\Simple\ClassBuffer $classBuffer)
	{
		return (new \AlexeyDsov\NsConverter\Buffers\Complex\AliasBuffer())
			->setNamespaceBuffer($nsBuffer)
			->setClassBuffer($classBuffer)
			;
	}

	private function getTestFileContent($fileNum = '')
	{
		return file_get_contents(
				$this->getDataPath('buffers' . DIRECTORY_SEPARATOR . "AliasBufferTest{$fileNum}.txt")
		);
	}
}