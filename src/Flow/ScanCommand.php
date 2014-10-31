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

namespace AlexeyDsov\NsConverter\Flow;

use AlexeyDsov\NsConverter\AddUtils\CMDUtils;
use AlexeyDsov\NsConverter\Buffers\ClassBuffer;
use AlexeyDsov\NsConverter\Buffers\ClassStorageBuffer;
use AlexeyDsov\NsConverter\Buffers\DefineConstantBuffer;
use AlexeyDsov\NsConverter\Buffers\NamespaceBuffer;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use AlexeyDsov\NsConverter\Utils\FormErrorWriter;
use AlexeyDsov\NsConverter\Utils\OutputMsg;
use AlexeyDsov\NsConverter\Utils\PathListGetter;

class ScanCommand
{
	use OutputMsg, PathListGetter, FormErrorWriter {
		OutputMsg::msg insteadof FormErrorWriter;
	}

	public function run()
	{
		$form = $this->getForm()
			->import(CMDUtils::getOptionsList())
			->checkRules();

		if ($this->processFormError($form)) {
			return;
		}

		$classPathList = $this->getPathList($form);
		$this->scan($form, $classPathList);
	}

	private function scan(Form $form, array $pathList)
	{
		$classStorage = new ClassStorage();

		$constantBuffer = (new DefineConstantBuffer())
			->setClassStorage($classStorage);

		$namespaceBuffer = new NamespaceBuffer();
		$classBuffer = new ClassBuffer();
		$buffer = (new ClassStorageBuffer())
			->setClassStorage($classStorage)
			->setNamespaceBuffer($namespaceBuffer)
			->setClassBuffer($classBuffer);

		foreach ($pathList as $path => $namespace) {
			$subjects = token_get_all(file_get_contents($path));
			$buffer->setNewNamespace($namespace)->init();
			foreach ($subjects as $i => $subject) {
				$buffer->process($subject, $i);
				$constantBuffer->process($subject, $i);
			}
		}
		print $classStorage->export($form->getValue('--current'))."\n";
	}

	/**
	 * @return Form
	 */
	private function getForm()
	{
		$form = Form::create()
			->add(Primitive::boolean('--current'));
		$this->fillFormWithPath($form);
		return $form;
	}
}
