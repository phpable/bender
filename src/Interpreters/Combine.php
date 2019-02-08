<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\TTargetable;
use \Able\Bender\Abstractions\TNested;

use \Able\Bender\Utilities\Registry;

use \Able\IO\File;

use \Exception;
use \Generator;

class Combine
	extends AExecutable {

	use TNested;
	use TTargetable;

	/**
	 * @param string $line
	 * @return Generator|null
	 *
	 * @throws Exception
	 */
	public function interpretate(string $line): ?Generator {
		foreach ($this->targets($line) as $Target) {
			yield from $Target->toReader()->read();
		}
	}
}
