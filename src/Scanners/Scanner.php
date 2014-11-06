<?php

namespace AlexeyDsov\NsConverter\Scanners;

use Evenement\EventEmitter;

interface Scanner
{
	public function init(EventEmitter $eventEmitter);
} 