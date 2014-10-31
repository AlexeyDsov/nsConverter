<?php

namespace AlexeyDsov\NsConverter\Test;

use \PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
	protected $backupGlobals = false;

	protected function getDataPath($postfix) {
		return dirname(__DIR__).DIRECTORY_SEPARATOR .'data'.DIRECTORY_SEPARATOR.ltrim($postfix, DIRECTORY_SEPARATOR);
	}
}
?>