<?php

namespace Onphp\NsConverter\Business;

use \Onphp\NsConverter\Auto\Business\AutoNsConstant;

class NsConstant extends AutoNsConstant implements NsObject
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