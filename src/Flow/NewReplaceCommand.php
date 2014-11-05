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

use AlexeyDsov\NsConverter\Business\ActionEnum;
use AlexeyDsov\NsConverter\Business\NsConfig;
use AlexeyDsov\NsConverter\Business\NsPath;
use AlexeyDsov\NsConverter\Utils\NamespaceUtils;
use AlexeyDsov\NsConverter\Utils\OutputMsg;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use AlexeyDsov\NsConverter\Buffers\CodeStorage;
use AlexeyDsov\NsConverter\Buffers\NamespaceBuffer;
use AlexeyDsov\NsConverter\Buffers\ClassBuffer;
use AlexeyDsov\NsConverter\Buffers\AliasBuffer;
use AlexeyDsov\NsConverter\Buffers\FunctionBuffer;
use AlexeyDsov\NsConverter\Buffers\ClassNameDetectBuffer;
use AlexeyDsov\NsConverter\Buffers\ChainBuffer;
use AlexeyDsov\NsConverter\Utils\CodeConverter;
use AlexeyDsov\NsConverter\Utils\CodeConverterException;
use AlexeyDsov\NsConverter\Utils\PathListGetter2;

class NewReplaceCommand
{
	use OutputMsg;

	public function run(NsConfig $config, ClassStorage $storage)
	{
		foreach ($this->getReplacePaths($config) as $nsPath) {

			$listGetter = (new PathListGetter2())
				->setNsPath($nsPath);

			$codeStorage = new CodeStorage();
			$namespaceBuffer = new NamespaceBuffer();
			$classBuffer = new ClassBuffer();
			$aliasBuffer = (new AliasBuffer())
				->setClassBuffer($classBuffer);
			$functionBuffer = new FunctionBuffer();
			$classNameDetectBuffer = (new ClassNameDetectBuffer())
				->setNamespaceBuffer($namespaceBuffer)
				->setClassBuffer($classBuffer)
				->setFunctionBuffer($functionBuffer)
				->setAliasBuffer($aliasBuffer);

			$chainBuffer = (new ChainBuffer())
				->addBuffer($codeStorage)
				->addBuffer($namespaceBuffer)
				->addBuffer($classBuffer)
				->addBuffer($aliasBuffer)
				->addBuffer($functionBuffer)
				->addBuffer($classNameDetectBuffer);

			foreach ($listGetter->getPathList() as $path => $newNamespace) {
				$subjects = token_get_all(file_get_contents($path));

				$chainBuffer->init();
				$className = null;
				foreach ($subjects as $i => $subject) {
					$chainBuffer->process($subject, $i);
					if ($className == null && $classBuffer->getClassName()) {
						$className = NamespaceUtils::fixNamespace(
							trim($newNamespace, '\\').'\\'.$classBuffer->getClassName()
						);
//						$className = ClassUtils::normalClassName(
//							trim($newNamespace, '\\').'\\'.$classBuffer->getClassName()
//						);
					}
				}

				$converter = new CodeConverter();
				$converter
					->setFilePath($path)
					->setCurrentClassName($className)
					->setNewNamespace($newNamespace)
					->setNamespaceBuffer($namespaceBuffer)
					->setClassStorage($storage)
					->setCodeStorage($codeStorage)
					->setClassNameDetectBuffer($classNameDetectBuffer)
					->setAliasBuffer($aliasBuffer)
					->setSkipUses($nsPath->isNoAlias());

				try {
					$converter->run();
				} catch (\Exception $e) {
					throw new CodeConverterException(
						'Exception while file ('.$path.') converting: '.
							print_r([get_class($e), $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(), $e->getTraceAsString()], true),
						null,
						$e
					);
				}

				file_put_contents($path, $codeStorage->toString());
			}
		}
	}

	/**
	 * @param NsConfig $config
	 * @return NsPath[]
	 */
	private function getReplacePaths(NsConfig $config)
	{
		return array_filter(
			$config->getPathes(),
			function (NsPath $path) {
				return $this->isReplacePath($path);
			}
		);
	}

	private function isReplacePath(NsPath $path)
	{
		return $path->getAction() == ActionEnum::REPLACE;
	}
}
