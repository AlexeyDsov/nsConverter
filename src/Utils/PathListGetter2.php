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

namespace AlexeyDsov\NsConverter\Utils;

use AlexeyDsov\NsConverter\Business\NsPath;
use Onphp\NamespaceResolverPSR0;
use Onphp\NamespaceResolverOnPHP;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Onphp\NamespaceResolver;

class PathListGetter2
{
	/**
	 * @param NsPath $path
	 * @return $this
	 */
	public function setNsPath(NsPath $path)
	{
		$this->nsPath = $path;
		return $this;
	}
	
	/**
	 * @param NamespaceResolver $resolver
	 * @return array (path => namespace)
	 */
	public function getPathList()
	{
		$path = realpath($this->nsPath->getPath());
		if (is_file($path)) {
			return [$path => NamespaceUtils::fixNamespace($this->nsPath->getNamespace())];
		}
		
		$resolver = $this->getNamespaceResolver();
		
		$classPathList = $resolver->getClassPathList();
		$pathList = [];
		foreach ($classPathList as $key => $value) {
			if (!is_numeric($key)) {
				list($namespace, $classname) = NamespaceUtils::explodeFullName($key);
				$path = realpath($classPathList[$value])
					.'/'.$classname.$resolver->getClassExtension();
				$pathList[$path] = $namespace;
			}
		}
		return $pathList;
	}
	
	/**
	 * @return NamespaceResolver
	 */
	private function getNamespaceResolver()
	{
		if ($this->nsPath->isPsr0()) {
			$resolver = NamespaceResolverPSR0::create();
			if ($ext = $this->nsPath->getExt()) {
				$resolver->setClassExtension($ext);
			}
			$resolver->setAllowedUnderline(false);
			$resolver->addPath(realpath($this->nsPath->getPath()), $this->nsPath->getNamespace());
			return $resolver;
		}
		
		$resolver = NamespaceResolverOnPHP::create();
		if ($ext = $this->nsPath->getExt()) {
			$resolver->setClassExtension($ext);
		}
		
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(realpath($this->nsPath->getPath()))
		);
		$pathList = [];
		foreach ($iterator as $key => $path) {
			if (is_dir($key)) {
				if (preg_match('~\.\.$~', $key)) {
					continue;
				}
				$pathList[] = $key;
			}
		}
		
		return $resolver->addPaths($pathList, $this->nsPath->getNamespace());
	}
}
