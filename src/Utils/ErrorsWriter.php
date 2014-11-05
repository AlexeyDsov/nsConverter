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

namespace ALexeyDsov\NsConverter\Utils;

trait ErrorWriter
{
	use OutputMsg;
	
	public function processErrors(array $errors)
	{
		foreach ($this->getErrorsLines($errors) as $message) {
			$this->msg($message);
		}
	}

	private function getErrorsLines(array $errors)
	{
		foreach ($errors as $field => $message) {
			if (!is_array($message)) {
				yield "{$field}: ".$message;
			} else {
				foreach ($this->getErrorsLines($message) as $subMessage) {
					yield $field.'.'.$subMessage;
				}
			}
		}
	}
}
