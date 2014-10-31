<?php
/***************************************************************************
 *   Copyright (C) 2011 by Alexey Denisov                                  *
 *   alexeydsov@gmail.com                                                  *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

namespace AlexeyDsov\NsConverter\AddUtils;

use \Onphp\CallbackLogicalObject;
use \Closure;
use \Onphp\Form;

/**
 * @deprecated
**/
class CallbackLogicalObjectSuccess extends CallbackLogicalObject
{
	/**
	 * @static
	 * @param Closure $callback
	 * @return CallbackLogicalObjectSuccess
	 */
	static public function create($callback)
	{
		return new self($callback);
	}

	/**
	 * @param Form $form
	 * @return bool
	 */
	public function toBoolean(Form $form)
	{
		parent::toBoolean($form);
		return true;
	}
}