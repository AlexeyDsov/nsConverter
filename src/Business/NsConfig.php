<?php


namespace AlexeyDsov\NsConverter\Business;


class NsConfig
{
	private $conf;
	/**
	 * @var NsPath[]
	 */
	private $pathes = [];

	/**
	 * @return mixed
	 */
	public function getConf()
	{
		return $this->conf;
	}

	/**
	 * @param string $conf
	 * @return $this
	 */
	public function setConf($conf)
	{
		$this->conf = $conf;
		return $this;
	}

	/**
	 * @return NsPath[]
	 */
	public function getPathes()
	{
		return $this->pathes;
	}

	/**
	 * @param NsPath[] $pathes
	 * @return $this
	 */
	public function setPathes($pathes)
	{
		$this->pathes = $pathes;
		return $this;
	}

	/**
	 * @param NsPath $path
	 * @return $this
	 */
	public function addPath(NsPath $path)
	{
		$this->pathes[] = $path;
		return $this;
	}
} 