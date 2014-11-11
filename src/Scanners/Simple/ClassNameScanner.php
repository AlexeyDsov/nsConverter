<?php

namespace AlexeyDsov\NsConverter\Scanners\Simple;

use AlexeyDsov\NsConverter\Scanners\Scanner;
use Evenement\EventEmitter;

class ClassNameScanner implements Scanner
{
	private static $excludeNames = [
		'null',
		'true',
		'false',
		'parent',
		'self',
		'static',
	];

	public function init(EventEmitter $ee)
	{
		$prevSubject = null;
		$buffer = false;
		$className = '';
		$classNameStart = null;
		$subjectsBuffer = [];
		$canStart = function ($num, $subject, EventEmitter $ee) use (&$prevSubject, &$buffer, &$className, &$classNameStart, &$subjectsBuffer) {
			if (!$buffer) {
				if ($this->canStart($subject, $prevSubject)) {
					$buffer = true;
					$className = $subject[1];
					$classNameStart = $num;
					$subjectsBuffer = [$num => $subject];
				}
			} else {
				$subjectsBuffer[$num] = $subject;
				if ($this->canStart($subject, $prevSubject)) {
					$className .= $subject[1];
				} elseif (is_array($subject) && $subject[0] == T_WHITESPACE) {
					//we'll skip spaces
				} else {
					$classNameEnd = $this->getEndSubject($subjectsBuffer, $num - 1);
					if (preg_match('~^[\\\\A-Z]~u', $className)) {
						$ee->emit('classNameDetected', [$className, $classNameStart, $classNameEnd]);
					}
					$buffer = false;
					$className = '';
					$classNameStart = null;
					$subjectsBuffer = [];
				}
			}
			$prevSubject = $subject;
		};


		$ee->on('token', $canStart);
	}

	private function start(EventEmitter $ee)
	{

	}

	private function canStart($subject, $prevSubject = null)
	{
		if ($prevSubject) {
			$isOkSubject = is_array($subject) && in_array($subject[0], [T_NS_SEPARATOR, T_STRING]);
			$isNokPrevSubject = is_array($prevSubject)
				&& in_array($prevSubject[0], [T_OBJECT_OPERATOR, T_PAAMAYIM_NEKUDOTAYIM, T_CONST]);
			$isExcludeNames = is_array($subject)
				&& $subject[0] == T_STRING
				&& in_array(mb_strtolower($subject[1]), self::$excludeNames);
			return $isOkSubject && !$isNokPrevSubject && !$isExcludeNames;
		}
		return false;
	}

	/**
	 * @param int $i
	 * @return int
	 */
	private function getEndSubject($subjects, $i)
	{
		while (isset($subjects[$i])) {
			$subject = $subjects[$i];
			if (is_array($subject) && $subject[0] == T_WHITESPACE) {
				$i--;
				continue;
			} else {
				break;
			}
		}
		return $i;
	}
}
