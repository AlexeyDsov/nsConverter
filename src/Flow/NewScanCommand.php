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

use AlexeyDsov\NsConverter\Buffers\Simple\ClassBuffer;
use AlexeyDsov\NsConverter\Buffers\Complex\ClassStorageBuffer;
use AlexeyDsov\NsConverter\Buffers\Simple\DefineConstantBuffer;
use AlexeyDsov\NsConverter\Buffers\Simple\NamespaceBuffer;
use AlexeyDsov\NsConverter\Business\ActionEnum;
use AlexeyDsov\NsConverter\Business\NsConfig;
use AlexeyDsov\NsConverter\Business\NsPath;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use AlexeyDsov\NsConverter\Utils\OutputMsg;
use AlexeyDsov\NsConverter\Utils\PathListGetter2;

class NewScanCommand
{
	use OutputMsg;

	public function run(NsConfig $config, ClassStorage $classStorage)
	{
		if (!($scanPaths = $this->getScanPaths($config))) {
			$this->msg("no pathes for scan");
		}

		$constantBuffer = (new DefineConstantBuffer())
			->setClassStorage($classStorage);

		$namespaceBuffer = new NamespaceBuffer();
		$classBuffer = new ClassBuffer();
		$buffer = (new ClassStorageBuffer())
			->setClassStorage($classStorage)
			->setNamespaceBuffer($namespaceBuffer)
			->setClassBuffer($classBuffer);

		foreach ($scanPaths as $scanPath) {
			$pathListGetter = (new PathListGetter2())
				->setNsPath($scanPath);

			foreach ($pathListGetter->getPathList() as $path => $namespace) {
				$subjects = token_get_all(file_get_contents($path));
				$buffer->setNewNamespace($namespace)->init();
				foreach ($subjects as $i => $subject) {
					$buffer->process($subject, $i);
					$constantBuffer->process($subject, $i);
				}
			}
		}
	}

	/**
	 * @param NsConfig $config
	 * @return NsPath[]
	 */
	private function getScanPaths(NsConfig $config)
	{
		return array_filter(
			$config->getPathes(),
			function (NsPath $path) {
				return $this->isScanPath($path);
			}
		);
	}

	/**
	 * @param NsPath $pathConfig
	 * @return NsPath
	 */
	private function isScanPath(NsPath $pathConfig)
	{
		return in_array($pathConfig->getAction(), [ActionEnum::SCAN, ActionEnum::REPLACE]);
	}
}
