<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\TNested;
use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\TTargetable;
use \Able\Bender\Abstractions\TInterpretatable;

use \Exception;
use \Generator;

class Combine
	extends AExecutable {

	use TNested;
	use TTargetable;
	use TInterpretatable;

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final function parseInterpretatable(string $line): bool {
		foreach ($this->targets($line) as $Target) {
			array_push($this->Stack, $Target->toReader()->read());
		}

		return true;
	}
}
