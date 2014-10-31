<?php

namespace AlexeyDsov\NsConverter\Business;

class NsConstant
{
	protected $name = null;

	/**
	 * @return NsConstant
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
	 * @return NsConstant
	 **/
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}
?>