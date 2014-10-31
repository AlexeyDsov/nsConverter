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

use AlexeyDsov\NsConverter\AddUtils\CMDUtils;
use AlexeyDsov\NsConverter\Utils\OutputMsg;
use AlexeyDsov\NsConverter\Utils\WrongStateException;

class AllCommand
{
	use OutputMsg;

	const CONFIG = '--config';
	const ACTION = '--action';

	public function run()
	{
		$cmdParams = CMDUtils::getOptionsList();

		$json = $errors = [];
		$path = $action = '';
		try {
			$path = $this->getConfigPath($cmdParams);
		} catch (\Exception $e) {
			$errors['-Dconfig'] = $e->getMessage();
		}
		try {
			$action = $this->getAction($cmdParams);
		} catch (\Exception $e) {
			$errors['-Daction'] = $e->getMessage();
		}
		if ($path) {
			try {
				$json = $this->parseJson($path);
			} catch (\Exception $e) {
				$errors['-Dconfig'] = $e->getMessage();
			}
		}
		if (!empty($errors)) {
			$this->msg('Next params errors:');
			foreach ($errors as $param => $msg) {
				print $this->msg($param.': '.$msg);
			}
			return;
		}

		$configCommand = new ConfigCommand();
		$configCommand->setAction($action);
		$configCommand->run($json);
	}

	private function parseJson($path)
	{
		if ($json = json_decode(file_get_contents($path), true)) {
			return $json;
		}
		$jsonError = json_last_error();
		if ($jsonError) {
			throw new WrongStateException("Json parse error {$jsonError}\n");
		}
		throw new WrongStateException("empty or incorrect json in --config\n");
	}

	private function getAction(array $cmdParams = [])
	{
		if (!isset($cmdParams['-Daction'])) {
			throw new WrongStateException("-Daction=[scan|replace] not found in arguments");
		} elseif (!in_array($action = $cmdParams['-Daction'], ['scan', 'replace'])) {
			throw new WrongStateException("-Daction=[scan|replace] wrong value");
		}
		return $action;
	}

	private function getConfigPath(array $cmdParams = [])
	{
		if (isset($cmdParams['-Dconfig'])) {
			$this->assertConfigPath($path = $cmdParams['-Dconfig']);
		} else {
			$this->assertConfigPath($path = 'config.json');
		}
		return $path;
	}

	private function assertConfigPath($path)
	{
		if (!file_exists($path)) {
			throw new WrongStateException("config path does not exists: ".$path);
		} elseif (!is_readable($path)) {
			throw new WrongStateException("config path not readable: ".$path);
		} elseif (!is_file($path)) {
			throw new WrongStateException("config path not a file: ".$path);
		}
	}
}
