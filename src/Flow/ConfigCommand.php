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

namespace AlexeyDsov\NsConverter\Flow;

use AlexeyDsov\NsConverter\Business\NsConfig;
use AlexeyDsov\NsConverter\Utils\ClassStorage;
use ALexeyDsov\NsConverter\Utils\ErrorWriter;
use AlexeyDsov\NsConverter\Utils\OutputMsg;
use AlexeyDsov\NsConverter\Utils\UnimplementedFeatureException;
use AlexeyDsov\NsConverter\Utils\WrongStateException;

class ConfigCommand
{
	use OutputMsg, ErrorWriter {
		OutputMsg::msg insteadof ErrorWriter;
	};

	private $action;

	public function setAction($action)
	{
		$this->action = $action;
	}

	public function run(array $jsonConfig)
	{
		if (!$this->action) {
			throw new WrongStateException('setAction first');
		}

		$config = new NsConfig();
		$errors = (new ConfigParser())->fillConfig($config, $jsonConfig);

		if ($errors) {
			$this->msg("config validation errrors:");
			$this->processErrors($errors);
			return;
		}

		$classStorage = $this->spawnClassStorage();

		if ($this->action == 'scan') {
			$this->doScan($config, $classStorage);
		} elseif ($this->action == 'replace') {
			$this->doReplace($config, $classStorage);
		} else {
			throw new UnimplementedFeatureException("not expected --action");
		}
	}

	private function doScan(NsConfig $config, ClassStorage $classStorage)
	{
		$scanCommand = new NewScanCommand();
		$scanCommand->run($config, $classStorage);
		file_put_contents($config->getConf(), $classStorage->export(false));
		print "scan finished success\n";
	}

	private function doReplace(NsConfig $config, ClassStorage $classStorage)
	{
		$confPath = $config->getConf();
		if (!file_exists($confPath)) {
			print "do action --scan first";
			return;
		}
		$classStorage->import(file_get_contents($confPath));

		$replaceCommand = new NewReplaceCommand();
		$replaceCommand->run($config, $classStorage);
	}

	/**
	 * @return ClassStorage
	 */
	private function spawnClassStorage()
	{
		$classStorage = new ClassStorage();
		$classStorage->import(file_get_contents(dirname(dirname(__DIR__)).'/data/php.ns'));

		return $classStorage;
	}
}
