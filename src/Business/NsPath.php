<?php

namespace AlexeyDsov\NsConverter\Business;

class NsPath
{
	private $action;
	private $path;
	private $ext;
	private $namespace;
	private $psr0 = true;
	private $noAlias = false;

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param mixed $action
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExt()
	{
		return $this->ext;
	}

	/**
	 * @param mixed $ext
	 * @return $this
	 */
	public function setExt($ext)
	{
		$this->ext = $ext;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param mixed $namespace
	 * @return $this
	 */
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param mixed $path
	 * @return $this
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isPsr0()
	{
		return $this->psr0;
	}

	/**
	 * @param boolean $psr0
	 * @return $this
	 */
	public function setPsr0($psr0)
	{
		$this->psr0 = $psr0 == true;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isNoAlias()
	{
		return $this->noAlias;
	}

	/**
	 * @param boolean $noAlias
	 * @return $this
	 */
	public function setNoAlias($noAlias)
	{
		$this->noAlias = $noAlias == true;
		return $this;
	}
} 