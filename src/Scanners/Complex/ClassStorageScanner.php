<?php

namespace AlexeyDsov\NsConverter\Scanners\Complex;

use AlexeyDsov\NsConverter\Business\NsClass;
use AlexeyDsov\NsConverter\Scanners\Scanner;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use Evenement\EventEmitter;

class ClassStorageScanner implements Scanner
{
	/**
	 * @var ClassStorage
	 */
	private $classStorage;
	private $newNamespace = '';

	/**
	 * @param ClassStorage $classStorage
	 * @return $this
	 */
	public function setClassStorage(ClassStorage $classStorage)
	{
		$this->classStorage = $classStorage;
		return $this;
	}

	/**
	 * @param string $newNamespace
	 * @return $this
	 */
	public function setNewNamespace($newNamespace)
	{
		$this->newNamespace = $newNamespace;
		return $this;
	}

	public function init(EventEmitter $ee)
	{
		$currentNamespace = '';

		$inNamespace = function ($namespaceName) use (&$currentNamespace) {
			$currentNamespace = $namespaceName;
		};
		$outNamespace = function () use (&$currentNamespace) {
			$currentNamespace = null;
		};
		$classStarted = function ($className) use (&$currentNamespace) {
			$class = NsClass::create()
				->setName($className)
				->setNamespace($currentNamespace)
				->setNewNamespace($this->newNamespace);
			$this->classStorage->addClass($class);
		};

		$ee->on('inNamespace', $inNamespace);
		$ee->on('outNamespace', $outNamespace);
		$ee->on('classStarted', $classStarted);
	}
}