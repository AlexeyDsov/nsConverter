<?php

namespace AlexeyDsov\NsConverter\Business;

use AlexeyDsov\NsConverter\Utils\NamespaceUtils;
use AlexeyDsov\NsConverter\Utils\NsObject;

class NsClass implements NsObject
{
	protected $name = null;
	protected $namespace = null;
	protected $newNamespace = null;

	/**
	 * @return NsClass
	 **/
	public static function create()
	{
		return new static;
	}

	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return NsClass
	 **/
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @return NsClass
	 **/
	public function setNamespace($namespace)
	{
		$this->namespace = NamespaceUtils::fixNamespace($namespace);

		return $this;
	}

	public function getNewNamespace()
	{
		return $this->newNamespace;
	}

	/**
	 * @return NsClass
	 **/
	public function setNewNamespace($newNamespace)
	{
		$this->newNamespace = NamespaceUtils::fixNamespace($newNamespace);

		return $this;
	}

	public function getFullName()
	{
		return $this->getNamespace().$this->getName();
	}

	public function getFullNewName()
	{
		return $this->getNewNamespace().$this->getName();
	}
}