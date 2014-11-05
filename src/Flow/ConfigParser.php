<?php

namespace AlexeyDsov\NsConverter\Flow;

use AlexeyDsov\NsConverter\Business\ActionEnum;
use AlexeyDsov\NsConverter\Business\NsConfig;
use AlexeyDsov\NsConverter\Business\NsPath;
use AlexeyDsov\NsConverter\Utils\NamespaceUtils;

class ConfigParser
{
	public function fillConfig(NsConfig $config, array $json)
	{
		$errors = [];
		$json += ['conf' => null, 'pathes' => []];

		$config->setConf($confPath = $json['conf']);
		if ($error = $this->checkConfPath($confPath)) {
			$errors['conf'] = $confPath;
		}
		if (is_array($pathes = $json['pathes'])) {
			if ($error = $this->fillPathes($config, $pathes)) {
				$errors['pathes'] = $error;
			}
		} else {
			$errors['pathes'] = 'No pathes in config';
		}

		return $errors;
	}

	private function fillPathes(NsConfig $config, array $pathes)
	{
		$errors = [];
		foreach ($pathes as $num => $pathJson) {
			if ($error = $this->fillPath($config, $pathJson)) {
				$errors[$num] = $pathJson;
			}
		}
		return $errors;
	}

	private function fillPath(NsConfig $config, array $json)
	{
		$json += [
			'action' => 'scan',
			'path' => null,
			'psr0' => true,
			'namespace' => '',
			'ext' => '.php',
			'noAlias' => false,
		];
		$nsPath = new NsPath();
		$nsPath
			->setAction($action = $json['action'])
			->setExt($ext = $json['ext'])
			->setNamespace(NamespaceUtils::fixNamespace($namespace = $json['namespace']))
			->setPsr0($json['psr0'])
			->setPath($path = $json['path']);

		$errors = [];
		if ($error = $this->checkAction($action)) {
			$errors['action'] = $error;
		}
		if ($error = $this->checkPath($path)) {
			$errors['path'] = $error;
		}

		$config->addPath($nsPath);

		return $errors;
	}

	private function checkAction($action)
	{
		if (!in_array($action, $names = array_keys(ActionEnum::getNames()))) {
			return 'wrong value, allowed: '.implode(',', $names);
		}
	}

	private function checkPath($path)
	{
		if (!$path) {
			return 'required value';
		}
	}

	private function checkConfPath($path)
	{
		if (!file_exists($path)) {
			$parentDir = dirname($path);
			if (!is_readable($parentDir)) {
				return $parentDir.' not readable';
			} elseif (!is_writable($parentDir)) {
				return $parentDir.' not writeable';
			}
		} elseif (!is_readable($path)) {
			return $path.' not readable';
		} elseif (!is_writable($path)) {
			return $path.' not writable';
		} elseif (!is_file($path) && !is_dir($path)) {
			return $path.' not dir and not file';
		}
	}
} 