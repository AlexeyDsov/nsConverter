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

namespace AlexeyDsov\NsConverter\AddUtils;

class ConsoleValueSelector
{
	private $list = [];

	/**
	 * @param array $list
	 * @return ConsoleValueSelector
	 */
	public function setList(array $list)
	{
		$this->list = $list;
		return $this;
	}

	public function readValue()
	{
		if ($key = $this->readValue()) {
			return $this->list[$key];
		}
	}

	public function readKey()
	{
		if (empty($this->list))
			return;

		while (true) {
			foreach ($this->list as $key => $value) {
				print "[{$key}] {$value}\n";
			}
			print "--------------------------\n";
			print "Please write your string key: ";
			$key = trim((new ConsoleReader())->readString());

			if (isset($this->list[$key])) {
				return $key;
			}
		}
	}
}